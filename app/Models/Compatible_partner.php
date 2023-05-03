<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compatible_partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'description',
        'more_compatible_months',
        'more_compatible_dates',
        'less_compatible_months',
        'less_compatible_dates',
    ];
}
