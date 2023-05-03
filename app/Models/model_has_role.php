<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class model_has_role extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'model_id');
    }

    public function userprofile()
    {
        return $this->hasOne(Useronboarding::class, 'user_id', 'model_id');
    }
}
