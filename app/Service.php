<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'partner_id', 'service', 'unit', 'price'
    ];


}
