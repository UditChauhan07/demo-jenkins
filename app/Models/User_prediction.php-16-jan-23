<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_prediction extends Model
{
    use HasFactory;

    protected $fillable =[
        "sender_id",
        'receiver_id',
        'dailyprediction_id',
        'message',
        'is_seen'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

}
