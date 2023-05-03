<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compatibility_description extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'number',
        'description',
    ];
}
