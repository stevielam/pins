<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    //
    
    protected $table = 'manual';
    protected $fillable = [ 
        'mode',
        'end_time',
        'relay_id'
    ];

    public function relay(){
        return $this->belongsTo('App\Relay');
    }
}
