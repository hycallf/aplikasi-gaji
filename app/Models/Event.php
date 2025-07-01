<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['nama_event'];

    // Relasi: Satu event bisa punya banyak insentif
    public function incentives()
    {
        return $this->hasMany(Incentive::class);
    }
}

