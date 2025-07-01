<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use DataTables;
use Carbon\Carbon;
use App\Models\Employee;

class EventController extends Controller
{
    /**
     * Menampilkan halaman daftar event dengan DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Event::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editButton = view('components.action-button', ['type' => 'edit', 'href' => route('events.edit', $row->id)])->render();
                    $deleteButton = view('components.action-button', ['type' => 'delete', 'route' => route('events.destroy', $row->id)])->render();
                    return '<div class="flex items-center justify-center">' . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action'])
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
       $request->validate(['nama_event' => 'required|string|max:255|unique:events,nama_event']);
        Event::create($request->only('nama_event'));
        return redirect()->route('events.index')->with('success', 'Jenis Event baru berhasil dibuat!');
    }

    /**
     * Menampilkan detail event.
     */
    public function show(Event $event)
    {
        // Eager load daftar insentif beserta data karyawannya
        $event->load('incentives.employee');

        // Ambil ID karyawan yang sudah ada di event ini
        $existingEmployeeIds = $event->incentives->pluck('employee_id');

        // Ambil daftar karyawan yang aktif DAN belum ada di event ini
        $employees = Employee::where('status', 'aktif')
                            ->whereNotIn('id', $existingEmployeeIds)
                            ->get();

        return view('events.show', compact('event', 'employees'));
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
        $request->validate(['nama_event' => 'required|string|max:255|unique:events,nama_event,' . $event->id]);
        $event->update($request->only('nama_event'));
        return redirect()->route('events.index')->with('success', 'Jenis Event berhasil diperbarui.');
    }

    /**
     * DITAMBAHKAN: Menghapus data event dari database.
     */
    public function destroy(Event $event)
    {
        if ($event->incentives()->exists()) {
            return redirect()->route('events.index')->with('error', 'Jenis Event ini tidak bisa dihapus karena sudah digunakan.');
        }
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Jenis Event berhasil dihapus.');
    }
}