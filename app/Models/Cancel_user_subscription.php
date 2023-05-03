<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cancel_user_subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'subscription_id',
        'type_date',
    ];
}
