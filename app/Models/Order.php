<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";

    protected $fillable = [
        'customer_name',
        'price',
        'currency_id',
        'holder_name',
        'card_no',
        'expired_month',
        'expired_year',
        'card_cvv'
    ];
}
