<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crypto_exchange_pair extends Model
{
    protected $table = 'crypto_exchange_pair';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ["From", "Exchange_id", "To"];
    protected $fillable = ['From', 'Exchange_id', "To", "Value"];

    public function exchange()
    {
        return $this->hasOne('App\Exchange', 'Exchange_id', 'Exchange_id');
    }

    public function cryptocurrency()
    {
        return $this->hasOne('App\Cryptocurrencies', 'Crypto_id', 'Crypto_id');
    }
}
