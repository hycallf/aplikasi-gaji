<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal_potongan',
        'jenis_potongan',
        'jumlah_potongan',
        'keterangan',
        'sumber' // DITAMBAHKAN: untuk menandai asal potongan
    ];

    // Relasi ke Employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}