<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Primaryno_type extends Model
{
    use HasFactory;

    protected $fillable = [
    'description' ,
            'positive' ,
            'negative' ,
            'occupations' ,
            'health' ,
            'partners' ,
            'times_of_the_year' ,
            'countries' ,
            'tibbits' ,
    ];
}
