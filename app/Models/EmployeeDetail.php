<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    protected $fillable = [
    'employee_id', 'foto', 'alamat', 'domisili', 'no_hp', 'no_ktp', 
    'status_pernikahan', 'jumlah_anak', 'tanggal_masuk'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
