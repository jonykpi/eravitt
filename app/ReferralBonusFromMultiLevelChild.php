<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferralBonusFromMultiLevelChild extends Model
{

protected $fillable = [
    "user_id",
    "child_id",
    "level",
    "wallet_id",
    "amount",
    "status",
    "type"
];
}
