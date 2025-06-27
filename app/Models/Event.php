<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['nama_event', 'deskripsi', 'start_date', 'end_date',];

    // Relasi: Satu event bisa punya banyak insentif
    public function incentives()
    {
        return $this->hasMany(Incentive::class);
    }
}

