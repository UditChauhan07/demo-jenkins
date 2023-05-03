<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription_prize extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'prize',
        'subscription_time',
    ];
}
