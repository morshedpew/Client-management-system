<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public $table = "sources";

    protected $fillable = [
        'name'
    ];

    public function clients()
    {
        return $this->hasMany('App\Client');
    }
}
