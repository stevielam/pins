<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relay extends Model
{
    //
    protected $table = 'relays';
    protected $fillable = ['name', 'is_output'];


    public function autoSchedules(){
        return $this->hasMany('App\Auto');
    }

    public function manualSchedules(){
        return $this->hasMany('App\Manual');
    }
    


}
