<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    public $table = "lead_statuses";

    protected $fillable = [
        'name'
    ];

    public function clients()
    {
        return $this->hasMany('App\Client');
    }
}
