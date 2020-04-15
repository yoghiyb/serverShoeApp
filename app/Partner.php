<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Partner as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\Authenticatable;

class Partner extends Authenticatable implements JWTSubject
{
    // use Notifiable;

    protected $fillable = [
        'name', 'email', 'address', 'start_working_time', 'end_working_time', 'start_working_days', 'end_working_days', 'phone_number', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function location(){
        return $this->hasOne('App\Location');
    }

    public function service(){
        return $this->hasMany('App\Service');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
