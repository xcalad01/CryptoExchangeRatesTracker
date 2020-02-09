<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crypto_exchange_pair extends Model
{
    protected $table = 'crypto_exchange_pair';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ["Crypto_id", "Exchange_id", "From", "To"];
    protected $fillable = ['Crypto_id', 'Exchange_id', "From", "To", "Value"];

    public function exchange()
    {
        return $this->hasOne('App\Exchange', 'Exchange_id', 'Exchange_id');
    }

    public function cryptocurrency()
    {
        return $this->hasOne('App\Cryptocurrencies', 'Crypto_id', 'Crypto_id');
    }
}
