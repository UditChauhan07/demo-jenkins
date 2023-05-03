<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_travel extends Model
{
    use HasFactory;
    protected $fillable =[
        "user_id",
        'type',
        'going_for',
        'date_from',
        'date_to'
    ];
}
