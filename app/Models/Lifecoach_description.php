<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lifecoach_description extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'star_type',
        'star_number',
        'number',
        'description',
    ];
}
