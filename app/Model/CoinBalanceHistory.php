<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CoinBalanceHistory extends Model
{
    protected $fillable = [
        'price',
        'previous_price',
        'updated_by',
    ];
}
