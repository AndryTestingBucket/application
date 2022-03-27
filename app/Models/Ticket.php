<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';

    protected $fillable = [
        'id',
        'uid',
        'subject',
        'user_name',
        'user_email',
        'created_at',
        'updated_at',
    ];

    public function message(){
        return $this->hasMany(Message::class,'ticket_id','id');
    }

}
