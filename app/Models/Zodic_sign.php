<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zodic_sign extends Model
{
    use HasFactory;
	public $table = 'zodiac_signs';
    protected $fillable = [
        'zodic_sign',
        'zodic_number',
        'zodic_day',
    ];
}
