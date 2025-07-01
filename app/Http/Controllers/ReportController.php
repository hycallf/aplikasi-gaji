<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Deduction;
use App\Models\Incentive;
use App\Models\Overtime;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // <-- Import DOMPDF
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
            ->where('periode_tahun', '>=', $year) // Perbaikan kecil di sini
            ->get();

        $period = Carbon::create($year, $month)->isoFormat('MMMM YYYY');

        // Load view, kirim data, dan buat PDF
        $pdf = PDF::loadView('reports.payroll_report', compact('payrolls', 'period'));

        // Download PDF dengan nama file dinamis
        return $pdf->download('laporan-gaji-' . $month . '-' . $year . '.pdf');
    }

    /**
     * Download slip gaji per karyawan sebagai PDF.
     */


    public function downloadPayslip(Payroll $payroll)
    {
        // Load relasi utama
        $payroll->load(['employee.detail', 'employee.user']);

        $year = $payroll->periode_tahun;
        $month = $payroll->periode_bulan;
        $employee_id = $payroll->employee_id;

        // --- Ambil Semua Data Detail ---
        // DITAMBAHKAN: Query untuk mengambil data absensi yang terlewat
        $statusesDapatTransport = ['hadir', 'pulang_awal'];
        $attendances = Attendance::where('employee_id', $employee_id)
            ->whereYear('date', $year)->whereMonth('date', '>=', $month) // Perbaikan kecil di sini
            ->whereIn('status', $statusesDapatTransport)
            ->get();
        $overtimes = Overtime::where('employee_id', $employee_id)
            ->whereYear('tanggal_lembur', $year)->whereMonth('tanggal_lembur', $month)->get();

        $incentives = Incentive::with('event')->where('employee_id', $employee_id)
            ->whereYear('tanggal_insentif', $year)
            ->whereMonth('tanggal_insentif', $month)
            ->get();

        $incentiveSummary = $incentives->groupBy('event.nama_event')
        ->map(function ($items, $eventName) {
            return [
                'event_name' => $eventName,
                'count' => $items->count(), // Hitung berapa kali terjadi
                'total_amount' => $items->sum('jumlah_insentif'), // Hitung total nominalnya
            ];
        });
        // --- LOGIKA BARU UNTUK MEMPROSES POTONGAN ---
        $allDeductions = Deduction::where('employee_id', $employee_id)
            ->whereYear('tanggal_potongan', $year)->whereMonth('tanggal_potongan', $month)->get();

        // Pisahkan potongan 'pulang_awal' dari yang lain
        $pulangAwalDeductions = $allDeductions->where('jenis_potongan', 'Transport')->where('sumber', 'absensi');
        $manualDeductions = $allDeductions->where('sumber', 'manual');

        // Siapkan data ringkasan untuk dikirim ke view
        $deductionSummary = [
            'pulang_awal_count' => $pulangAwalDeductions->count(),
            'pulang_awal_total' => $pulangAwalDeductions->sum('jumlah_potongan'),
            'manual_deductions' => $manualDeductions
        ];
        // --- AKHIR LOGIKA BARU ---

        $pdf = PDF::loadView('reports.payslip', compact('payroll', 'attendances', 'overtimes', 'incentives', 'deductionSummary','incentiveSummary'));

        return $pdf->stream('slip-gaji-' . $payroll->employee->nama . '-' . $month . '-' . $year . '.pdf');
    }
}
