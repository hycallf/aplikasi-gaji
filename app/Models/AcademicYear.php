<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tahun_ajaran',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(DosenMatkulEnrollment::class);
    }

    /**
     * Scope untuk mendapatkan tahun ajaran aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Method untuk mengaktifkan tahun ajaran ini
     * dan menonaktifkan yang lain
     */
    public function activate()
    {
        // Nonaktifkan semua tahun ajaran lain
        static::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Aktifkan tahun ajaran ini
        $this->is_active = true;
        $this->save();
    }

    /**
     * Accessor untuk nama lengkap
     */
    public function getNamaLengkapAttribute()
    {
        return "{$this->nama_tahun_ajaran} - Semester " . ucfirst($this->semester);
    }

    /**
     * Method untuk cek apakah tahun ajaran sedang berlangsung
     */
    public function isCurrent()
    {
        $now = Carbon::now();
        return $now->between($this->tanggal_mulai, $this->tanggal_selesai);
    }
}
