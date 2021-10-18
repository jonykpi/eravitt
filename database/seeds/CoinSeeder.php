<?php

use App\Model\Coin;
use App\Model\Wallet;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            Coin::updateOrCreate(['type' => DEFAULT_COIN_TYPE], ['type' => DEFAULT_COIN_TYPE, 'name' => settings('coin_name')]);

            $users = User::select('*')->get();
            if (isset($users[0])) {
                foreach ($users as $user) {
                    $coins = Coin::select('*')->get();
                    $count = $coins->count();
                    for($i=0; $count > $i; $i++) {
                        Wallet::updateOrCreate(['user_id' => $user->id, 'coin_type' => $coins[$i]->type],
                            ['name' =>  $coins[$i]->type.' Wallet','user_id' => $user->id, 'coin_type' => $coins[$i]->type, 'coin_id' => $coins[$i]->id]);
                    }
                }
            }
            $wallets = Wallet::where('coin_id', null)->get();
            if (isset($wallets[0])) {
                foreach ($wallets as $wallet) {
                    $coin = Coin::where(['type' => $wallet->coin_type])->first();
                    $wallet->update(['coin_id' => $coin->id]);
                }
            } else {

            }
        } catch (\Exception $e) {
        }
    }
}
