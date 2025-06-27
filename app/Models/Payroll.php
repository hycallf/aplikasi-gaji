<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- TAMBAHKAN INI

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'periode_bulan', 'periode_tahun', 'gaji_pokok',
        'total_tunjangan_transport', 'total_upah_lembur', 'total_insentif',
        'total_potongan', 'gaji_kotor', 'gaji_bersih'
    ];

    /**
     * DITAMBAHKAN: Mendefinisikan bahwa satu record Payroll "milik" satu Employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}