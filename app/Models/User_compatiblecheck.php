<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_compatiblecheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'type_name',
        'name',
        'email',
        'gender',
        'dates',
        'type_dates',
        'number',
        'postalcode',
        'city',
        'no_of_partner',
    ];
}
