<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'partner_id', 'order_no', 'name', 'address', 'phone_number', 'description', 'amount', 'price', 'status'
    ];

    
}
