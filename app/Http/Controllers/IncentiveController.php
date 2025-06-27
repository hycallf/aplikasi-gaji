<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Incentive;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    /**
     * Menyimpan data insentif baru untuk seorang karyawan pada event tertentu.
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            // employee_ids sekarang adalah sebuah array, dan setiap elemen di dalamnya harus ada di tabel employees
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'jumlah_insentif' => 'required|numeric|min:0',
        ]);

        // Ambil semua ID karyawan yang dipilih dari form
        $employeeIds = $request->input('employee_ids');
        $jumlahInsentif = $request->input('jumlah_insentif');

        // Gunakan perulangan untuk menambahkan setiap karyawan ke event
        foreach ($employeeIds as $employeeId) {
            $event->incentives()->updateOrCreate(
                [
                    'employee_id' => $employeeId, // Kunci untuk mencari
                ],
                [
                    'jumlah_insentif' => $jumlahInsentif, // Set jumlahnya (sama untuk semua yang dipilih)
                ]
            );
        }

        return back()->with('success', count($employeeIds) . ' karyawan berhasil ditambahkan/diperbarui insentifnya.');
    }
    /**
     * Menghapus data insentif seorang karyawan dari sebuah event.
     */
    public function destroy(Incentive $incentive)
    {
        $incentive->delete();
        return back()->with('success', 'Data insentif berhasil dihapus.');
    }
}