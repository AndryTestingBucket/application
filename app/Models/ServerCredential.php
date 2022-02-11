<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerCredential extends Model
{
    use HasFactory;

    protected $table = 'servercredentials';

    protected $fillable = [
        'id',
        'ftp_login',
        'ftp_password',
    ];
}
