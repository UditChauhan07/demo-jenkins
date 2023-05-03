<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module_type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'parent',
    ];
}
