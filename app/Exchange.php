<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'exchanges';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = 'Exchange_id';
    protected $fillable = ['Exchange_id', 'Name', 'Image', 'Url'];
}
