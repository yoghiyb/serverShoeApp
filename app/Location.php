<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable =[
        'id_partner', 'latitude', 'longtitude'
    ];

    public function partner(){
        return $this->belongsTo('App\Partner');
    }
}
