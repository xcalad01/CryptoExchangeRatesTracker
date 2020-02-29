<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CryptoHistorical extends Model
{
    protected $table = 'crypto_historical';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['id', 'Timestamp'];
    protected $fillable = ['id', 'Timestamp', 'Open', 'High', 'Low', 'Close', 'Volume'];

    public function historical_available()
    {
        return $this->hasOne('App\Historical_available');
    }
}
