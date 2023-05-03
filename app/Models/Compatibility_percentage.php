<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compatibility_percentage extends Model
{
    use HasFactory;

    protected $fillable = [
        'compatibility_number',
        'compatibility_percentage',
        'strength',
    ];
}
