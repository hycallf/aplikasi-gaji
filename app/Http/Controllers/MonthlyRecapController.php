<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRecapController extends Controller
{
    /**
     * Menampilkan halaman form rekap bulanan.
     */
    public function index(Request $request)
    {
        $employees = Employee::where('tipe_karyawan', 'karyawan')
                         ->where('status', 'aktif')
                         ->orderBy('nama')->get();
        return view('recap.index', compact('employees'));
    }

    /**
     * Menyimpan data rekap bulanan ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'hadir' => 'required|integer|min:0',
            'sakit' => 'required|integer|min:0',
            'izin' => 'required|integer|min:0',
            'pulang_awal' => 'required|integer|min:0',
            'total_lembur' => 'nullable|numeric|min:0',
        ]);

        $employeeId = $validated['employee_id'];
        $month = $validated['month'];
        $year = $validated['year'];
        $startDate = Carbon::create($year, $month, 1);
        $daysInMonth = $startDate->daysInMonth;

        DB::beginTransaction();
        try {
            // Hapus data absensi & lembur lama untuk karyawan & periode ini
            Attendance::where('employee_id', $employeeId)->whereMonth('date', $month)->whereYear('date', $year)->delete();
            Overtime::where('employee_id', $employeeId)->whereMonth('tanggal_lembur', $month)->whereYear('tanggal_lembur', $year)->delete();
            Deduction::where('employee_id', $employeeId)->whereMonth('tanggal_potongan', $month)->whereYear('tanggal_potongan', $year)->where('sumber', 'absensi')->delete();

            $statuses = [
                'hadir' => $validated['hadir'],
                'sakit' => $validated['sakit'],
                'izin' => $validated['izin'],
                'pulang_awal' => $validated['pulang_awal'],
            ];

            // Loop sebanyak hari di bulan tersebut
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($year, $month, $day);

                // Lewati hari Minggu
                if ($currentDate->isSunday()) {
                    continue;
                }

                // Cari status untuk diisi pada hari ini
                $statusToInsert = null;
                foreach($statuses as $status => &$count) { // Gunakan reference (&)
                    if ($count > 0) {
                        $statusToInsert = $status;
                        $count--; // Kurangi jatah hari untuk status ini
                        break;
                    }
                }

                // Buat record absensi hanya jika masih ada jatah hari
                if($statusToInsert) {
                    Attendance::create([
                        'employee_id' => $employeeId,
                        'date' => $currentDate->toDateString(),
                        'status' => $statusToInsert,
                    ]);

                    // Jika pulang awal, buat potongan
                    if ($statusToInsert === 'pulang_awal') {
                        Deduction::create([
                            'employee_id' => $employeeId,
                            'tanggal_potongan' => $currentDate->toDateString(),
                            'sumber' => 'absensi',
                            'jenis_potongan' => 'Transport',
                            'jumlah_potongan' => 10000,
                            'keterangan' => 'Potongan transport karena pulang awal',
                        ]);
                    }
                }
            }

            // Jika ada input total lembur, buat satu record lembur
            if (!empty($validated['total_lembur']) && $validated['total_lembur'] > 0) {
                Overtime::create([
                    'employee_id' => $employeeId,
                    'tanggal_lembur' => $startDate->endOfMonth()->toDateString(),
                    'deskripsi_lembur' => 'Akumulasi lembur periode ' . $startDate->isoFormat('MMMM Y'),
                    'upah_lembur' => $validated['total_lembur'],
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data rekap: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('recap.index')->with('success', 'Data rekap bulanan berhasil disimpan!');
    }
}
