<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InactivityLog extends Model
{

    protected $fillable = ['employee_id', 'tipe', 'keterangan', 'start_date', 'end_date'];
    public function inactivityLogs()
    {
        return $this->hasMany(InactivityLog::class)->orderBy('start_date', 'desc');
    }

    // Untuk mendapatkan data ketidakaktifan yang paling baru (yang sedang berjalan)
    public function latestInactivityLog()
    {
        return $this->hasOne(InactivityLog::class)->latestOfMany();
    }
}
