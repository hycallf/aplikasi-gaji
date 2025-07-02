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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class PayrollController extends Controller
{
    // app/Http/Controllers/PayrollController.php
public function index(Request $request)
{
    // Jika ini adalah request AJAX dari DataTables
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

            // Kolom Transport dengan Tombol Detail
            ->addColumn('transport', function($row) {
                $total = 'Rp ' . number_format($row->total_tunjangan_transport, 0, ',', '.');
                $button = '<button @click.prevent="showDetails('.$row->id.', \'transport\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
            })
            // Kolom Lembur dengan Tombol Detail
            ->addColumn('lembur', function($row) {
                $total = 'Rp ' . number_format($row->total_upah_lembur, 0, ',', '.');
                $button = '<button @click.prevent="showDetails('.$row->id.', \'lembur\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
            })
            // Kolom Insentif dengan Tombol Detail
            ->addColumn('insentif', function($row) {
                $total = 'Rp ' . number_format($row->total_insentif, 0, ',', '.');
                $button = '<button @click.prevent="showDetails('.$row->id.', \'insentif\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                return '<div class="flex items-center justify-end">'.$total.$button.'</div>';
            })
            // Kolom Potongan dengan Tombol Detail
            ->addColumn('potongan', function($row) {
                $total = 'Rp ' . number_format($row->total_potongan, 0, ',', '.');
                $button = '<button @click.prevent="showDetails('.$row->id.', \'potongan\')" class="ml-2 text-blue-500 hover:underline"><i class="fa-solid fa-eye fa-xs"></i></button>';
                return '<div class="flex items-center justify-end text-red-600">'.$total.$button.'</div>';
            })

            ->editColumn('gaji_bersih', fn($row) => '<span class="font-bold">Rp ' . number_format($row->gaji_bersih, 0, ',', '.') . '</span>')

            // Kolom Aksi untuk Slip Gaji
            ->addColumn('action', function($row){
                 // Nanti link ini akan kita buat
                return '<a href="'.route('payslip.download', $row->id).'" target="_blank" class="text-green-600 hover:underline">Cetak Slip</a>';
            })
            ->rawColumns(['transport', 'lembur', 'insentif', 'potongan', 'gaji_bersih', 'action'])
            ->make(true);
        }


        // --- LOGIKA PROSES OTOMATIS SAAT MEMBUKA HALAMAN ---
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Cek apakah payroll untuk bulan ini sudah pernah diproses
        $isProcessed = Payroll::where('periode_bulan', $currentMonth)
                                ->where('periode_tahun', $currentYear)
                                ->exists();

        // Jika belum, proses secara otomatis
        if (!$isProcessed) {
            $this->calculateAndStorePayroll($currentMonth, $currentYear);
        }
        // --- AKHIR LOGIKA PROSES OTOMATIS ---
        // Untuk tampilan awal halaman
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

        // Cukup panggil method private yang sudah ada
        $this->calculateAndStorePayroll($request->month, $request->year);

        return redirect()->route('payroll.index', ['month' => $request->month, 'year' => $request->year])
                         ->with('success', 'Payroll untuk periode ' . Carbon::create($request->year, $request->month)->isoFormat('MMMM Y') . ' berhasil diproses ulang.');
    }

    // app/Http/Controllers/PayrollController.php

    private function calculateAndStorePayroll($month, $year)
    {
        // Ambil semua karyawan aktif beserta relasi matkul mereka
        $employees = Employee::with('matkuls')->where('status', 'aktif')->get();

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                // Inisialisasi semua komponen gaji dengan 0
                $gajiPokok = 0;
                $tunjangan = 0;
                $totalTransport = 0;
                $totalLembur = 0;
                $totalInsentif = 0;
                $totalPotongan = 0;

                // --- LOGIKA PERHITUNGAN BERDASARKAN TIPE KARYAWAN ---

                if ($employee->tipe_karyawan === 'dosen') {
                // =============== PERHITUNGAN KHUSUS DOSEN ===============

                    $dosenAttendances = DosenAttendance::with('matkul')
                        ->where('employee_id', $employee->id)
                        ->where('periode_bulan', $month)->where('periode_tahun', $year)
                        ->get();

                    // DITAMBAHKAN: Inisialisasi variabel dengan nilai 0
                    $honorariumSks = 0;

                    $totalPertemuan = $dosenAttendances->sum('jumlah_pertemuan');

                    foreach ($dosenAttendances as $attendance) {
                        // Pengecekan aman jika relasi matkul tidak ada
                        if ($attendance->matkul) {
                            $sks = $attendance->matkul->sks ?? 0;
                            $honorariumSks += (7500 * $sks * $attendance->jumlah_pertemuan);
                        }
                    }

                    // Transport utama dosen
                    $transportUtama = $totalPertemuan * ($employee->transport ?? 0);

                    $gajiPokok = $employee->gaji_pokok ?? 0;
                    $tunjangan = $employee->tunjangan ?? 0;
                    $totalTransport = $transportUtama + $honorariumSks;

                } else {
                    // =============== PERHITUNGAN UNTUK KARYAWAN BIASA ===============
                    $gajiPokok = $employee->gaji_pokok ?? 0;
                    $tunjangan = $employee->tunjangan ?? 0;
                    $statusesDapatTransport = ['hadir', 'pulang_awal'];
                    $jumlahHariMasuk = Attendance::where('employee_id', $employee->id)
                        ->whereYear('date', $year)->whereMonth('date', $month)
                        ->whereIn('status', $statusesDapatTransport)->count();
                    $totalTransport = $jumlahHariMasuk * ($employee->transport ?? 0);
                }

                // Komponen lain yang berlaku untuk semua (lembur, insentif, potongan)
                $totalLembur = Overtime::where('employee_id', $employee->id)
                    ->whereYear('tanggal_lembur', $year)->whereMonth('tanggal_lembur', $month)
                    ->sum('upah_lembur');

                $totalInsentif = Incentive::where('employee_id', $employee->id)
                    ->whereYear('tanggal_insentif', $year)->whereMonth('tanggal_insentif', $month)
                    ->sum('jumlah_insentif');

                $totalPotongan = Deduction::where('employee_id', $employee->id)
                    ->whereYear('tanggal_potongan', $year)->whereMonth('tanggal_potongan', $month)
                    ->sum('jumlah_potongan');

                // Final Kalkulasi
                $gajiKotor = $gajiPokok + $tunjangan + $totalTransport + $totalLembur + $totalInsentif;
                $gajiBersih = $gajiKotor - $totalPotongan;

                // Simpan atau Update ke tabel payrolls
                Payroll::updateOrCreate(
                    ['employee_id' => $employee->id, 'periode_bulan' => $month, 'periode_tahun' => $year],
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

        // Jika yang diklik adalah Dosen dan tipe detailnya adalah 'transport'
        if ($payroll->employee->tipe_karyawan === 'dosen' && $type === 'transport') {
            $dosenAttendances = DosenAttendance::with('matkul')
                ->where('employee_id', $employee_id)
                ->where('periode_bulan', $month)->where('periode_tahun', $year)
                ->get();

            return view('payroll._detail_dosen_modal', compact('payroll', 'dosenAttendances'));
        }

        switch ($type) {
            case 'transport':
                $statuses = ['hadir', 'pulang_awal'];
                // DITAMBAHKAN: orderBy('date', 'asc') untuk mengurutkan dari tanggal terawal
                $data = Attendance::where('employee_id', $employee_id)
                    ->whereYear('date', $year)->whereMonth('date', $month)
                    ->whereIn('status', $statuses)
                    ->orderBy('date', 'asc') // <-- Mengurutkan data
                    ->get();
                $total = $data->count() * $payroll->employee->transport;
                break;

            case 'lembur':
                // DITAMBAHKAN: orderBy untuk mengurutkan
                $data = Overtime::where('employee_id', $employee_id)
                    ->whereYear('tanggal_lembur', $year)->whereMonth('tanggal_lembur', $month)
                    ->orderBy('tanggal_lembur', 'asc') // <-- Mengurutkan data
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
                $total = $data->sum('jumlah_insentif');
                break;

            case 'potongan':
                // DITAMBAHKAN: orderBy untuk mengurutkan
                $data = Deduction::where('employee_id', $employee_id)
                    ->whereYear('tanggal_potongan', $year)->whereMonth('tanggal_potongan', $month)
                    ->orderBy('tanggal_potongan', 'asc') // <-- Mengurutkan data
                    ->get();
                $total = $data->sum('jumlah_potongan');
                break;
        }

        // Kirim juga data payroll agar bisa akses tarif transport di view
        return view('payroll._detail_modal', compact('data', 'type', 'total', 'payroll'));
    }
}
