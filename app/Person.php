<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public $table = "persons";

    protected $fillable = [
        'name'
    ];

    public function clients()
    {
        return $this->hasMany('App\Client');
    }
}
