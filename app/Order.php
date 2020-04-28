<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'partner_id', 'service_id', 'order_no', 'name', 'address', 'phone_number', 'description', 'amount', 'price', 'status'
    ];

    public function service(){
        return $this->belongsTo('App\Service');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
