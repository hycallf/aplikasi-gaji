<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DosenAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'matkul_id',
        'periode_bulan',
        'periode_tahun',
        'jumlah_pertemuan',
    ];

    public function matkul(): BelongsTo
    {
        return $this->belongsTo(Matkul::class);
    }

    /**
     * DITAMBAHKAN: Relasi ke model Employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
