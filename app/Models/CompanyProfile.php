<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'nama_perwakilan',
        'alamat',
        'email_kontak',
        'no_telepon',
        'logo',
    ];
}
