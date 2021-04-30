<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $table = "clients";

    protected $fillable = [
        'name', 'company_name', 'conversion_date', 'contact_number', 'email', 'address', 'comment_1', 'comment_2'
    ];

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    public function source()
    {
        return $this->belongsTo('App\Source');
    }

    public function leadStatus()
    {
        return $this->belongsTo('App\LeadStatus');
    }

    public function meetings()
    {
        return $this->hasMany('App\Meeting');
    }
}
