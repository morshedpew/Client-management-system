<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    public $table = "meetings";

    protected $fillable = [
        'title', 'agenda', 'date', 'time'
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
