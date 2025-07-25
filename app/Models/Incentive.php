<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incentive extends Model
{
    protected $fillable = [
        'event_id',
        'employee_id',
        'tanggal_insentif',
        'quantity',
        'unit_amount',
        'total_amount',
        'deskripsi',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * DITAMBAHKAN (Opsional tapi bagus): Relasi ke Event.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
