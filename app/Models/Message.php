<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServerCredential;

class Message extends Model
{
    use HasFactory;

    protected $table = 'message';

    protected $fillable = [
        'id',
        'author',
        'content',
        'created_at',
        'updated_at',
    ];

    public function serverCredentials(){
        return $this->hasMany(ServerCredential::class,'message_id','id');
    }


}
