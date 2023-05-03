<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alphasystem_type extends Model
{
    use HasFactory;
    public function alphabet()
    {
        return $this->hasmany(System_type::class, 'id' ,'systemtype_id');
    }
}
