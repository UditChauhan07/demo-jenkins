<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fav_unfav_parameter extends Model
{
    use HasFactory;
    protected $fillable = [
        'numbers',
        'days',
        'months',
    ];
}
