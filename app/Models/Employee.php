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
        'user_id',
        'nama', // Asumsi Anda punya kolom ini
        'jabatan',
        'tipe_karyawan',
        'gaji_pokok',
        'transport',
        'tunjangan',
        'departemen',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(EmployeeDetail::class);
    }

    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'employee_matkul');
    }
}
