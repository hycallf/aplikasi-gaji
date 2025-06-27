<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $fillable = [
        'employee_id',
        'tanggal_lembur',
        'deskripsi_lembur',
        'upah_lembur',
    ];
}
