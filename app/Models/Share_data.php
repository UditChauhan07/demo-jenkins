<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share_data extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id' ,
                'otheruser_id' ,
                'module_type' ,
                'email' ,
        ];
}
