<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal_parameter extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'description',
        'love_relationship',
        'health',
        'career',
        'travel',
    ];
}
