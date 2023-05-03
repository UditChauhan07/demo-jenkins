<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daily_prediction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'prediction_date',
        'prediction',
        'publish_status',
    ];

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
