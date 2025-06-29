<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDetail extends Model
{
    protected $fillable = [
    'employee_id', 'foto', 'alamat', 'domisili', 'no_hp', 
    'status_pernikahan', 'jumlah_anak', 'tanggal_masuk','pendidikan_terakhir', 
    'jurusan'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
