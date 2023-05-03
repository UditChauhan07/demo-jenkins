<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner_relationship extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'mate_number',
        'description',
    ];
}
