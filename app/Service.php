<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'partner_id', 'service', 'unit', 'price'
    ];

    public function service(){
        return $this->hasOne('App\Order');
    }

    public function partner(){
        return $this->belongsTo('App\Partner');
    }
}
