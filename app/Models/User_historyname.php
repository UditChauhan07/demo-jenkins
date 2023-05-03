<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_historyname extends Model
{
    use HasFactory;
    protected $fillable =[
        "user_id",
        'name',
        'status',
        'check_date'
    ];
}
