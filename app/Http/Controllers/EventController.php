<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use DataTables;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Menampilkan halaman daftar event dengan DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Event::query();

            // --- BAGIAN FILTER YANG DIPERBAIKI ---
            $startDate = $request->filled('start_date_filter') ? Carbon::parse($request->start_date_filter)->startOfDay() : null;
            $endDate = $request->filled('end_date_filter') ? Carbon::parse($request->end_date_filter)->endOfDay() : null;

            // Jika hanya start_date yang diisi
            if ($startDate && !$endDate) {
                $query->where('start_date', '>=', $startDate);
            }
            
            // Jika hanya end_date yang diisi
            if (!$startDate && $endDate) {
                $query->where('start_date', '<=', $endDate);
            }
            
            // Jika keduanya diisi
            if ($startDate && $endDate) {
                // Logika untuk mencari event yang bersinggungan dengan rentang filter
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($sub) use ($startDate, $endDate) {
                        $sub->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
                });
            }
            // ... (logika filter tanggal tetap sama) ...

            $data = $query->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('periode', function($row){
                    $startDate = Carbon::parse($row->start_date)->isoFormat('D MMM YYYY');
                    $endDate = Carbon::parse($row->end_date)->isoFormat('D MMM YYYY');

                    // Jika tanggal mulai dan selesai sama, tampilkan satu tanggal saja
                    if ($startDate == $endDate) {
                        return $startDate;
                    }
                    
                    // Jika berbeda, tampilkan rentangnya
                    return $startDate . ' - ' . $endDate;
                })
                ->addColumn('action', function($row){
                    // --- BAGIAN YANG DIPERBARUI: MENGGUNAKAN KOMPONEN ---
                    $showButton = view('components.action-button', [
                        'type' => 'show',
                        'href' => route('events.show', $row->id)
                    ])->render();
                    
                    $editButton = view('components.action-button', [
                        'type' => 'edit',
                        'href' => route('events.edit', $row->id)
                    ])->render();

                    $deleteButton = view('components.action-button', [
                        'type' => 'delete',
                        'route' => route('events.destroy', $row->id),
                        'class' => 'delete-form'
                    ])->render();
                    
                    return '<div class="flex items-center justify-center space-x-2">' . $showButton . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action', 'periode'])
                ->make(true);
        }

        return view('events.index');
    }

    /**
     * Menampilkan form untuk membuat event baru.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Menyimpan event baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deskripsi' => 'nullable|string',
        ]);
        
        // Ambil semua data dari request
        $data = $request->all();

        // DITAMBAHKAN: Jika end_date kosong, samakan dengan start_date
        if (empty($data['end_date'])) {
            $data['end_date'] = $data['start_date'];
        }

        // Buat event dengan data yang sudah disesuaikan
        Event::create($data);

        // 3. Redirect kembali ke halaman daftar event dengan pesan sukses
        return redirect()->route('events.index')->with('success', 'Event baru berhasil dibuat!');
    }

    /**
     * Menampilkan detail event.
     */
    public function show(Event $event)
    {
        // Nanti akan kita kembangkan untuk kelola peserta
        return view('events.show', compact('event'));
    }

    /**
     * DITAMBAHKAN: Menampilkan form untuk mengedit event.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * DITAMBAHKAN: Mengupdate data event di database.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->all();

        // DITAMBAHKAN: Logika yang sama untuk update
        if (empty($data['end_date'])) {
            $data['end_date'] = $data['start_date'];
        }

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * DITAMBAHKAN: Menghapus data event dari database.
     */
    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();
            
            // Hapus user yang terhubung terlebih dahulu
            // Ini akan gagal jika ada relasi lain yang melindungi user,
            // tapi untuk kasus ini seharusnya aman.

            // Hapus data employee
            $event->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('events.index')->with('success', 'Data event berhasil dihapus.');
    }
}