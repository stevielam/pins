<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auto extends Model
{
    //
    protected $table = "auto";
    protected $fillable = [
        'master_enable', 
        'monday_enable', 
        'tuesday_enable', 
        'wednesday_enable', 
        'thursday_enable', 
        'friday_enable', 
        'saturday_enable', 
        'sunday_enable', 
        'start_time', 
        'end_time', 
        'relay_id'
    ];
    
    
    public function relay(){
        return $this->belongsTo('App\Relay');
    }
}
