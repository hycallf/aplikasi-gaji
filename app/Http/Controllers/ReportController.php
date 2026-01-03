<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Deduction;
use App\Models\Incentive;
use App\Models\Overtime;
use App\Models\Payroll;
use App\Models\DosenAttendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Download laporan payroll keseluruhan sebagai PDF.
     */
    public function downloadPayrollReport(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $payrolls = Payroll::with('employee')
            ->where('periode_bulan', $month)
            ->where('periode_tahun', '>=', $year)
            ->get();

        $period = Carbon::create($year, $month)->isoFormat('MMMM YYYY');

        // (Opsional) Jika perlu company profile, pastikan di-pass atau menggunakan ViewComposer
        // Disini saya fokus ke logic payrollnya saja sesuai request
        $pdf = PDF::loadView('reports.payroll_report', compact('payrolls', 'period'));

        return $pdf->download('laporan-gaji-' . $month . '-' . $year . '.pdf');
    }

    public function downloadPayslip(Payroll $payroll)
    {
        $payroll->load(['employee.detail', 'employee.user']);

        $year = $payroll->periode_tahun;
        $month = $payroll->periode_bulan;
        $employee_id = $payroll->employee_id;
        $tunjangan = $payroll->employee->tunjangan;

        // --- AMBIL SETTING TARIF SKS ---
        // Default 7500 jika setting tidak ditemukan
        $dosenRatePerSks = Setting::get('dosen_rate_per_sks', 7500);

        $attendances = collect();
        $dosenAttendances = collect();
        $overtimes = collect();
        $incentives = collect();
        $deductionSummary = [];

        if ($payroll->employee->tipe_karyawan === 'dosen') {
            $dosenAttendances = DosenAttendance::with('matkul')
                ->where('employee_id', $employee_id)
                ->where('periode_bulan', $month)->where('periode_tahun', $year)
                ->get();
        } else {
            $statusesDapatTransport = ['hadir', 'pulang_awal'];
            $attendances = Attendance::where('employee_id', $employee_id)
                ->whereYear('date', $year)->whereMonth('date', $month)
                ->whereIn('status', $statusesDapatTransport)->get();
        }

        $overtimes = Overtime::where('employee_id', $employee_id)
            ->whereYear('tanggal_lembur', $year)->whereMonth('tanggal_lembur', $month)->get();

        $incentives = Incentive::with('event')->where('employee_id', $employee_id)
            ->whereYear('tanggal_insentif', $year)
            ->whereMonth('tanggal_insentif', $month)
            ->get();

        $incentiveSummary = $incentives->groupBy('event_id')
            ->map(function ($items) {
                $firstItem = $items->first();
                if (!$firstItem) return null;
                return [
                    'event_name' => $firstItem->event->nama_event ?? 'Insentif Lainnya',
                    'count' => $items->sum('quantity'),
                    'unit_amount' => $firstItem->unit_amount,
                    'total_amount' => $items->sum('total_amount'),
                ];
            })->filter();

        $allDeductions = Deduction::where('employee_id', $employee_id)
            ->whereYear('tanggal_potongan', $year)->whereMonth('tanggal_potongan', $month)->get();

        $pulangAwalDeductions = $allDeductions->where('jenis_potongan', 'Transport')->where('sumber', 'absensi');
        $manualDeductions = $allDeductions->where('sumber', 'manual');

        $deductionSummary = [
            'pulang_awal_count' => $pulangAwalDeductions->count(),
            'pulang_awal_total' => $pulangAwalDeductions->sum('jumlah_potongan'),
            'manual_deductions' => $manualDeductions
        ];

        // --- PASSING VARIABLE $dosenRatePerSks KE VIEW ---
        $pdf = PDF::loadView('reports.payslip', compact(
            'payroll',
            'tunjangan',
            'attendances',
            'overtimes',
            'incentives',
            'deductionSummary',
            'incentiveSummary',
            'dosenAttendances',
            'dosenRatePerSks' // <--- DIKIRIM KE SINI
        ));

        return $pdf->stream('slip-gaji-' . $payroll->employee->nama . '-' . $month . '-' . $year . '.pdf');
    }
}
