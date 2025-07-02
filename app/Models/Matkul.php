<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Matkul extends Model
{
    use HasFactory;
    protected $fillable = ['nama_matkul', 'sks'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_matkul');
    }
}
