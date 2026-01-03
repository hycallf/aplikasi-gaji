<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DosenAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'enrollment_id',
        'matkul_id',
        'academic_year_id', // DITAMBAHKAN
        'periode_bulan',
        'periode_tahun',
        'jumlah_pertemuan',
        'kelas',
    ];

    protected $casts = [
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'jumlah_pertemuan' => 'integer',
    ];

    /**
     * Relasi ke Enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(DosenMatkulEnrollment::class, 'enrollment_id');
    }

    /**
     * Relasi ke Matkul (backward compatibility)
     */
    public function matkul(): BelongsTo
    {
        return $this->belongsTo(Matkul::class);
    }

    /**
     * Relasi ke Employee (Dosen)
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopePeriode($query, $month, $year)
    {
        return $query->where('periode_bulan', $month)
                    ->where('periode_tahun', $year);
    }

    /**
     * Scope untuk filter berdasarkan dosen
     */
    public function scopeByDosen($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
