<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historical_available extends Model
{
    protected $table = 'historical_available';
    public $timestamps = false;

    protected $fillable = ['From', 'To', 'Exchange_id'];

    public function exchange()
    {
        return $this->hasOne('App\Exchange', 'Exchange_id', 'Exchange_id');
    }
}
