<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\Event;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DataTables;

class IncentiveController extends Controller
{
    /**
     * Menyimpan data insentif baru untuk seorang karyawan pada event tertentu.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Incentive::with(['employee', 'event'])->latest();

            // Filter berdasarkan rentang tanggal jika ada input
            if ($request->filled('start_date_filter') && $request->filled('end_date_filter')) {
                $startDate = Carbon::parse($request->start_date_filter)->startOfDay();
                $endDate = Carbon::parse($request->end_date_filter)->endOfDay();
                $query->whereBetween('tanggal_insentif', [$startDate, $endDate]);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_karyawan', fn($row) => $row->employee->nama ?? 'N/A')
                ->addColumn('nama_event', fn($row) => $row->event->nama_event ?? 'N/A')
                ->editColumn('jumlah_insentif', fn($row) => 'Rp ' . number_format($row->jumlah_insentif, 0, ',', '.'))
                ->addColumn('action', function($row){
                    // Untuk insentif, kita hanya sediakan tombol hapus
                    return view('components.action-button', [
                        'type' => 'delete',
                        'route' => route('incentives.destroy', $row->id)
                    ])->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('incentives.index');
    }

    public function create()
        {
            $employees = Employee::where('status', 'aktif')->orderBy('nama')->get();
            $events = Event::orderBy('nama_event')->get(); // Ambil semua jenis event
            return view('incentives.create', compact('employees', 'events'));
        }

    public function store(Request $request)
    {
        // Log data mentah yang diterima untuk debugging
        Log::info('Incentive Store Request Data:', $request->all());

        // Validasi
        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'tanggal_insentif' => 'required|array|min:1',
            'tanggal_insentif.*' => 'required|date_format:Y-m-d',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'jumlah_insentif' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ], [
            'tanggal_insentif.required' => 'Tanggal insentif wajib dipilih.',
            'employee_ids.required' => 'Karyawan penerima wajib dipilih.',
        ]);

        try {
            // Gunakan DB::transaction yang lebih elegan
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData['employee_ids'] as $employeeId) {
                    foreach ($validatedData['tanggal_insentif'] as $dateString) {
                        Incentive::updateOrCreate(
                            [
                                'event_id' => $validatedData['event_id'],
                                'employee_id' => $employeeId,
                                'tanggal_insentif' => $dateString,
                            ],
                            [
                                'jumlah_insentif' => $validatedData['jumlah_insentif'],
                                'deskripsi' => $validatedData['deskripsi'],
                            ]
                        );
                    }
                }
            });
        } catch (\Exception $e) {
            // Log errornya untuk debugging
            Log::error('Incentive Store Failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan internal. Gagal menyimpan data.')->withInput();
        }

        return redirect()->route('incentives.index')->with('success', 'Insentif berhasil ditambahkan.');
    }
    /**
     * Menghapus data insentif seorang karyawan dari sebuah event.
     */
    public function destroy(Incentive $incentive)
    {
        $incentive->delete();
        return redirect()->route('incentives.index')->with('success', 'Data insentif berhasil dihapus.');
    }
}
