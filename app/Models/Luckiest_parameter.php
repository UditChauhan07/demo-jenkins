<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Luckiest_parameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'lucky_colours',
        'lucky_gems',
        'lucky_metals',
        'lucky_sports',
        'lucky_cars',
    ];
}
