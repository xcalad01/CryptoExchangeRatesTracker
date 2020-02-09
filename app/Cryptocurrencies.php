<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cryptocurrencies extends Model
{
    protected $table = 'cryptocurrencies';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'Crypto_id';
    protected $fillable = ['Crypto_id', 'Name'];
}
