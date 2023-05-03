<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Free_subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'number_of_users',
        'start_date',
        'end_date',
    ];
}
