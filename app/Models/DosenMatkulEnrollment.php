<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenMatkulEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'matkul_id',
        'academic_year_id',
        'kelas',
        'jumlah_mahasiswa',
        'catatan',
    ];

    protected $casts = [
        'jumlah_mahasiswa' => 'integer',
    ];

    /**
     * Relasi ke Employee (Dosen)
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relasi ke Matkul
     */
    public function matkul()
    {
        return $this->belongsTo(Matkul::class);
    }

    /**
     * Relasi ke Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relasi ke Dosen Attendances
     */
    public function attendances()
    {
        return $this->hasMany(DosenAttendance::class, 'enrollment_id');
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran aktif
     */
    public function scopeActiveYear($query)
    {
        return $query->whereHas('academicYear', function($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope untuk filter berdasarkan dosen tertentu
     */
    public function scopeByDosen($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Accessor untuk nama lengkap matkul dengan kelas
     */
    public function getMatkulLengkapAttribute()
    {
        $nama = $this->matkul->nama_matkul ?? 'N/A';
        return $this->kelas ? "{$nama} (Kelas {$this->kelas})" : $nama;
    }
}
