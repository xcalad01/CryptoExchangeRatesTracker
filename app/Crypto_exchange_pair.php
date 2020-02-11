<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Crypto_exchange_pair extends Model
{
    protected $table = 'crypto_exchange_pairs';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ["From", "Exchange_id", "To"];
    protected $fillable = ['From', 'Exchange_id', "To", "Value"];

    public function exchange()
    {
        return $this->hasOne('App\Exchange', 'Exchange_id');
    }

    public function cryptocurrency()
    {
        return $this->hasOne('App\Cryptocurrencies', 'Crypto_id');
    }
}
