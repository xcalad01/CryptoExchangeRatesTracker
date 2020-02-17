<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fiat_historical extends Model
{
    protected $table = 'fiat_historicals';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['Date', 'Fiat_id'];
    protected $fillable = ['Date', 'Fiat_id', 'Value_USD'];

    public function fiat()
    {
        return $this->hasOne('App\Fiat', 'Fiat_id', 'Fiat_id');
    }
}
