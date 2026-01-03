<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'nama',
        'nidn',
        'gelar_depan',
        'gelar_belakang',
        'jabatan',
        'tipe_karyawan',
        'status_dosen',
        'departemen',
        'gaji_pokok',
        'transport',
        'tunjangan',
        'status',
        'user_id',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'transport' => 'decimal:2',
        'tunjangan' => 'decimal:2',
    ];

    public function detail()
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function incentives()
    {
        return $this->hasMany(Incentive::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function latestInactivityLog()
    {
        return $this->hasOne(InactivityLog::class)->latestOfMany();
    }

    // ========== RELASI BARU UNTUK DOSEN ==========

    /**
     * Relasi ke Enrollments (Matkul yang diajar per tahun ajaran)
     */
    public function enrollments()
    {
        return $this->hasMany(DosenMatkulEnrollment::class);
    }

    /**
     * Relasi ke Dosen Attendances
     */
    public function dosenAttendances()
    {
        return $this->hasMany(DosenAttendance::class);
    }

    /**
     * Relasi Many-to-Many ke Matkul melalui Enrollments
     * (untuk tahun ajaran aktif)
     */
    public function matkulsActiveYear()
    {
        return $this->belongsToMany(Matkul::class, 'dosen_matkul_enrollments')
                    ->whereHas('academicYear', function($q) {
                        $q->where('is_active', true);
                    })
                    ->withPivot(['academic_year_id', 'kelas', 'jumlah_mahasiswa'])
                    ->withTimestamps();
    }

    /**
     * DEPRECATED: Relasi lama ke matkul (untuk backward compatibility)
     * Gunakan matkulsActiveYear() untuk implementasi baru
     */
    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'employee_matkul')
                    ->withTimestamps();
    }

    // ========== SCOPES ==========

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDosen($query)
    {
        return $query->where('tipe_karyawan', 'dosen');
    }

    public function scopeKaryawan($query)
    {
        return $query->where('tipe_karyawan', 'karyawan');
    }

    // ========== ACCESSORS & HELPERS ==========

    /**
     * Accessor untuk nama lengkap dengan gelar (khusus dosen)
     */
    public function getNamaLengkapAttribute()
    {
        if ($this->tipe_karyawan !== 'dosen') {
            return $this->nama;
        }

        $nama = $this->nama;
        if ($this->gelar_depan) {
            $nama = $this->gelar_depan . ' ' . $nama;
        }
        if ($this->gelar_belakang) {
            $nama .= ', ' . $this->gelar_belakang;
        }
        return $nama;
    }

    /**
     * Helper untuk cek apakah employee adalah dosen
     */
    public function isDosen()
    {
        return $this->tipe_karyawan === 'dosen';
    }

    /**
     * Helper untuk cek apakah employee adalah karyawan
     */
    public function isKaryawan()
    {
        return $this->tipe_karyawan === 'karyawan';
    }

}
