<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Life_cycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'cycle_by_month',
        'cycle_by_date',
        'cycle_by_year',
    ];
}
