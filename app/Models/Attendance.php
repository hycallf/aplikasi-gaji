<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // app/Models/Attendance.php
    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'description',
    ];
}
