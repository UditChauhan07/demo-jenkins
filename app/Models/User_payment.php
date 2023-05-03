<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "subscription_status",
        "subscription_id",
        "product_id",
        "prize_id",
        "amount",
        "plan_name",
        "created",
        "start_date",
        "renewal_date",
        "interval",
        "interval_count",
        "currency",
        "customer_id",
        "payment_id",
        "payment_method",     
        "receipt_url",
        "status",
        "latest_invoice",
    ];
}
