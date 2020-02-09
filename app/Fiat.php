<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fiat extends Model
{
    protected $table = 'fiats';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = ["Fiat_id"];
    protected $fillable = ['Fiat_id', 'Name', "Value_USD"];
}
