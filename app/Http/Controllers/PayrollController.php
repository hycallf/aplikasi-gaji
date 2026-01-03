<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DosenAttendance;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Incentive;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\AcademicYear;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $data = Payroll::with('employee')
                ->where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_karyawan', fn($row) => $row->employee->nama)
                ->editColumn('gaji_pokok', fn($row) => 'Rp ' . number_format($row->gaji_pokok, 0, ',', '.'))
                ->addColumn('transport', function($row) {
                    $total = 'Rp ' . number_format($row->total_tunjangan_transport, 0, ',', '.');
                    $button = '<button @click.prevent="showDetails('.$row->id.', \'transport\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                    return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
                })
                ->addColumn('tunjangan', fn($row) => 'Rp ' . number_format($row->employee->tunjangan ?? 0, 0, ',', '.'))
                ->addColumn('lembur', function($row) {
                    $total = 'Rp ' . number_format($row->total_upah_lembur, 0, ',', '.');
                    $button = '<button @click.prevent="showDetails('.$row->id.', \'lembur\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                    return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
                })
                ->addColumn('insentif', function($row) {
                    $total = 'Rp ' . number_format($row->total_insentif, 0, ',', '.');
                    $button = '<button @click.prevent="showDetails('.$row->id.', \'insentif\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                    return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
                })
                ->addColumn('potongan', function($row) {
                    $total = 'Rp ' . number_format($row->total_potongan, 0, ',', '.');
                    $button = '<button @click.prevent="showDetails('.$row->id.', \'potongan\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                    return '<div class="flex items-center justify-end text-red-600">'.$total.$button.'</div>';
                })
                ->editColumn('gaji_bersih', fn($row) => '<span class="font-bold">Rp ' . number_format($row->gaji_bersih, 0, ',', '.') . '</span>')
                ->addColumn('action', fn($row) => '<a href="'.route('payslip.download', $row->id).'" target="_blank" class="text-green-600 hover:underline">Cetak Slip</a>')
                ->rawColumns(['transport', 'lembur', 'insentif', 'potongan', 'gaji_bersih', 'action'])
                ->make(true);
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $isProcessed = Payroll::where('periode_bulan', $currentMonth)
                              ->where('periode_tahun', $currentYear)
                              ->exists();

        if (!$isProcessed) {
            $this->calculateAndStorePayroll($currentMonth, $currentYear);
        }

        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        return view('payroll.index', compact('selectedMonth', 'selectedYear'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        $this->calculateAndStorePayroll($request->month, $request->year);

        return redirect()->route('payroll.index', ['month' => $request->month, 'year' => $request->year])
                         ->with('success', 'Payroll untuk periode ' . Carbon::create($request->year, $request->month)->isoFormat('MMMM Y') . ' berhasil diproses ulang.');
    }

    /**
     * FIXED: Menggunakan enrollment system dan settings
     */
    private function calculateAndStorePayroll($month, $year)
    {
        // HANYA load relasi yang diperlukan - HAPUS relasi matkuls lama
        $employees = Employee::with(['enrollments.matkul', 'enrollments.academicYear'])
                            ->where('status', 'aktif')
                            ->get();

        // AMBIL SETTINGS
        $dosenRatePerSks = Setting::get('dosen_rate_per_sks', 7500);
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $gajiPokok = 0;
                $tunjangan = 0;
                $totalTransport = 0;
                $totalLembur = 0;
                $totalInsentif = 0;
                $totalPotongan = 0;

                if ($employee->tipe_karyawan === 'dosen') {
                    // =============== PERHITUNGAN DOSEN ===============

                    $dosenAttendances = DosenAttendance::with(['enrollment.matkul', 'enrollment.academicYear'])
                        ->where('employee_id', $employee->id)
                        ->where('periode_bulan', $month)
                        ->where('periode_tahun', $year)
                        ->whereNotNull('enrollment_id')
                        ->get();

                    $honorariumSks = 0;
                    $totalPertemuan = $dosenAttendances->sum('jumlah_pertemuan');

                    foreach ($dosenAttendances as $attendance) {
                        if ($attendance->enrollment && $attendance->enrollment->matkul) {
                            $sks = $attendance->enrollment->matkul->sks ?? 0;
                            $honorariumSks += ($dosenRatePerSks * $sks * $attendance->jumlah_pertemuan);
                        }
                    }

                    // Transport utama dosen (per pertemuan)
                    $transportUtama = $totalPertemuan * ($employee->transport ?? 0);

                    $gajiPokok = $employee->gaji_pokok ?? 0;
                    $tunjangan = $employee->tunjangan ?? 0;
                    $totalTransport = $transportUtama + $honorariumSks;

                } else {
                    // =============== PERHITUNGAN KARYAWAN BIASA ===============
                    $gajiPokok = $employee->gaji_pokok ?? 0;
                    $tunjangan = $employee->tunjangan ?? 0;

                    $statusesDapatTransport = ['hadir', 'pulang_awal'];

                    // Hitung hari masuk berdasarkan work_days setting
                    $jumlahHariMasuk = Attendance::where('employee_id', $employee->id)
                        ->whereYear('date', $year)
                        ->whereMonth('date', $month)
                        ->whereIn('status', $statusesDapatTransport)
                        ->where(function($query) use ($workDays) {
                            // Filter hanya hari kerja sesuai setting
                            foreach ($workDays as $day) {
                                $query->orWhereRaw('DAYOFWEEK(date) = ?', [$day == 0 ? 7 : $day + 1]);
                            }
                        })
                        ->count();

                    $totalTransport = $jumlahHariMasuk * ($employee->transport ?? 0);
                }

                // =============== KOMPONEN UNIVERSAL ===============
                $totalLembur = Overtime::where('employee_id', $employee->id)
                    ->whereYear('tanggal_lembur', $year)
                    ->whereMonth('tanggal_lembur', $month)
                    ->sum('upah_lembur');

                $totalInsentif = Incentive::where('employee_id', $employee->id)
                    ->whereYear('tanggal_insentif', $year)
                    ->whereMonth('tanggal_insentif', $month)
                    ->sum('total_amount');

                $totalPotongan = Deduction::where('employee_id', $employee->id)
                    ->whereYear('tanggal_potongan', $year)
                    ->whereMonth('tanggal_potongan', $month)
                    ->sum('jumlah_potongan');

                // =============== KALKULASI AKHIR ===============
                $gajiKotor = $gajiPokok + $tunjangan + $totalTransport + $totalLembur + $totalInsentif;
                $gajiBersih = $gajiKotor - $totalPotongan;

                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'periode_bulan' => $month,
                        'periode_tahun' => $year
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'total_tunjangan' => $tunjangan,
                        'total_tunjangan_transport' => $totalTransport,
                        'total_upah_lembur' => $totalLembur,
                        'total_insentif' => $totalInsentif,
                        'total_potongan' => $totalPotongan,
                        'gaji_kotor' => $gajiKotor,
                        'gaji_bersih' => $gajiBersih,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getDetails(Payroll $payroll, string $type)
    {
        $payroll->load('employee');
        $year = $payroll->periode_tahun;
        $month = $payroll->periode_bulan;
        $employee_id = $payroll->employee_id;
        $data = [];
        $total = 0;

        // Detail khusus dosen
        if ($payroll->employee->tipe_karyawan === 'dosen' && $type === 'transport') {
            $dosenAttendances = DosenAttendance::with(['enrollment.matkul', 'enrollment.academicYear'])
                ->where('employee_id', $employee_id)
                ->where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->whereNotNull('enrollment_id')
                ->get();

            $dosenRatePerSks = Setting::get('dosen_rate_per_sks', 7500);

            return view('payroll._detail_dosen_modal', compact('payroll', 'dosenAttendances', 'dosenRatePerSks'));
        }

        // Detail untuk karyawan biasa
        switch ($type) {
            case 'transport':
                $statuses = ['hadir', 'pulang_awal'];
                $data = Attendance::where('employee_id', $employee_id)
                    ->whereYear('date', $year)->whereMonth('date', $month)
                    ->whereIn('status', $statuses)
                    ->orderBy('date', 'asc')
                    ->get();
                $total = $data->count() * $payroll->employee->transport;
                break;

            case 'lembur':
                $data = Overtime::where('employee_id', $employee_id)
                    ->whereYear('tanggal_lembur', $year)->whereMonth('tanggal_lembur', $month)
                    ->orderBy('tanggal_lembur', 'asc')
                    ->get();
                $total = $data->sum('upah_lembur');
                break;

            case 'insentif':
                $data = Incentive::with('event')
                    ->where('employee_id', $employee_id)
                    ->whereYear('tanggal_insentif', $year)
                    ->whereMonth('tanggal_insentif', $month)
                    ->orderBy('tanggal_insentif', 'asc')
                    ->get();
                $total = $data->sum('total_amount');
                break;

            case 'potongan':
                $data = Deduction::where('employee_id', $employee_id)
                    ->whereYear('tanggal_potongan', $year)->whereMonth('tanggal_potongan', $month)
                    ->orderBy('tanggal_potongan', 'asc')
                    ->get();
                $total = $data->sum('jumlah_potongan');
                break;
        }

        return view('payroll._detail_modal', compact('data', 'type', 'total', 'payroll'));
    }
}
