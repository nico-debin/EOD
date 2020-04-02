<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EodStockDetail extends Model
{
    protected $table = 'eod_stock_details';
    protected $primaryKey = 'ticker';
    public $incrementing = false;
    protected $fillable = ['ticker'];
}
