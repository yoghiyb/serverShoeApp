<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable =[
        'partner_id', 'latitude', 'longitude'
    ];

    public function partner(){
        return $this->belongsTo('App\Partner');
    }
}
