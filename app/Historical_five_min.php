<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historical_five_min extends Model
{
    protected $table = 'historical_five_min';

    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ['id', 'Timestamp'];
    protected $fillable = ['id', 'Timestamp', 'Open', 'High', 'Low', 'Close', 'Volume'];

    public function historical_available()
    {
        return $this->hasOne('App\Historical_available');
    }
}
