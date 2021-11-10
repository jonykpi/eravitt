<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 8/30/17
 * Time: 5:19 PM
 */

namespace App\Repository;

use App\Http\Services\CommonService;
use App\Jobs\GiveCoin;
use App\Model\Bank;
use App\Model\BuyCoinHistory;
use App\Model\CoinRequest;
use App\Model\Wallet;
use App\Model\WalletAddressHistory;
use App\Services\CoinPaymentsAPI;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Stripe;


class CoinRepository
{
    //create wallet
    public function buyCoin($request)
    {
        $response = ['success' => false, 'message' => __('Invalid request')];
        try {
            $url = file_get_contents('https://min-api.cryptocompare.com/data/price?fsym=USD&tsyms=BTC');

            if (isset(\GuzzleHttp\json_decode($url, true)['BTC'])) {
                $coin_price_doller = bcmul($request->coin, settings('coin_price'),8);
                $coin_price_btc = bcmul(\GuzzleHttp\json_decode($url, true)['BTC'], $coin_price_doller,8);
                $coin_price_btc = number_format($coin_price_btc, 8);

                if ($request->payment_type == BTC) {
                    $btc_transaction = new BuyCoinHistories();
                    $btc_transaction->address = settings('admin_coin_address');
                    $btc_transaction->type = BTC;
                    $btc_transaction->user_id = Auth::id();
                    $btc_transaction->coin = $request->coin;
                    $btc_transaction->doller = $coin_price_doller;
                    $btc_transaction->btc = $coin_price_btc;
                    $btc_transaction->save();

                    $response = ['success' => true, 'message' => __('Request submitted successful,please send BTC with this address')];
                } elseif ($request->payment_type == CARD) {
                    $common_servie = new CommonService();
                    $all_req = $request->all();
                    $all_req['btn_amount'] = $coin_price_btc;
                    $all_req['total_coin_price_in_dollar'] = $coin_price_doller;
                    $trans = $common_servie->make_transaction((object)$all_req);
                    if ($trans['success']) {
                        DB::beginTransaction();
                        try {
                            $btc_transaction = new BuyCoinHistories();
                            $btc_transaction->type = CARD;
                            $btc_transaction->user_id = Auth::id();
                            $btc_transaction->coin = $request->coin;
                            $btc_transaction->address = $trans['data']->networkTransactionId;
                            $btc_transaction->doller = $coin_price_doller;
                            $btc_transaction->btc = $coin_price_btc;
                            $btc_transaction->status = STATUS_SUCCESS;
                            $btc_transaction->save();

                            //  add  coin on balance //
                            $default_wallet = Wallet::where('user_id', Auth::id())->where('is_primary', 1)->first();
                            $default_wallet->balance = $default_wallet->balance + $request->coin;
                            $default_wallet->save();

                            DB::commit();
                            $response = ['success' => true, 'message' => __('Coin purchased successfully')];

                            // all good
                        } catch (\Exception $e) {

                            DB::rollback();
                            $response = ['success' => false, 'message' => __('Something went wrong')];
                            return $response;
                        }
                    } else {
                        $response = ['success' => false, 'message' => $trans['message']];
                    }
                } elseif ($request->payment_type = BANK_DEPOSIT) {
                    $btc_transaction = new BuyCoinHistories();
                    $btc_transaction->type = BANK_DEPOSIT;
                    $btc_transaction->address = 'N/A';
                    $btc_transaction->user_id = Auth::id();
                    $btc_transaction->doller = $coin_price_doller;
                    $btc_transaction->btc = $coin_price_btc;
                    $btc_transaction->coin = $request->coin;
                    $btc_transaction->bank_id = $request->bank_id;
                    $btc_transaction->bank_sleep = uploadFile($request->file('sleep'), IMG_SLEEP_PATH);
                    $btc_transaction->save();

                    $response = ['success' => true, 'message' => __('Request submitted successful,Please wait for admin approval')];
                }
            } else {
                $response = ['success' => false, 'message' => __('Invalid request')];
            }

        } catch(\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // send coin amount request to user
    public function sendCoinAmountRequest($request)
    {
        try {
            $userAddress = WalletAddressHistory::leftjoin('wallets', 'wallets.id', '=', 'wallet_address_histories.wallet_id')
                ->leftjoin('coins', 'coins.id', '=', 'wallets.coin_id')
                ->where(['wallet_address_histories.address'=> $request->address])
                ->select('wallet_address_histories.address', 'wallets.*', 'coins.withdrawal_fees')
                ->first();

            if (isset($userAddress)) {
                if ($userAddress->user_id == Auth::id()) {
                    $response = ['success' => false, 'message' => __('You can not send request to your own address')];
                    return $response;
                }
                $myWallet = get_primary_wallet(Auth::id(), 'Default');
               //check balance

                if ($myWallet->balance < $request->amount){
                    $response = ['success' => false, 'message' => __('Amount cant be more then available balance')];
                    return $response;
                }


                try {
                    DB::beginTransaction();

                    $userWallet = get_primary_wallet($userAddress->user_id, 'Default');
                    $data = [
                        'amount' => $request->amount,
                        'sender_user_id' => Auth::id(),
                        'sender_wallet_id' => $myWallet->id,

                        'receiver_user_id' => $userAddress->user_id,
                        'receiver_wallet_id' => $userWallet->id,

                        'status' => STATUS_SUCCESS,
                        'fees' => check_withdrawal_fees($request->amount, $userAddress->withdrawal_fees)
                    ];
                    CoinRequest::create($data);

                    $add = Wallet::find($myWallet->id);
                    $add->decrement("balance",$request->amount+check_withdrawal_fees($request->amount, $userAddress->withdrawal_fees));
                    $add->save();

                    $deduct = Wallet::find($userWallet->id);
                    $deduct->increment("balance",$request->amount);
                    $deduct->save();

                    DB::commit();
                }catch (\Exception $exception){
                    DB::rollBack();
                }
                $response = ['success' => true, 'message' => __('DPV sent successfully to this address')];
            } else {
                $response = ['success' => false, 'message' => __('User not found')];
            }

        } catch (\Exception $e) {
            Log::info('sendCoinAmountRequest -> '.$e->getMessage());
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // give coin amount request to user
    public function giveCoinToUser($request)
    {
        try {
            $userAddress = WalletAddressHistory::leftjoin('wallets', 'wallets.id', '=', 'wallet_address_histories.wallet_id')
                ->leftjoin('coins', 'coins.id', '=', 'wallets.coin_id')
                ->where(['wallet_address_histories.address'=> $request->address])
                ->select('wallet_address_histories.address', 'wallets.*', 'coins.withdrawal_fees')
                ->first();
            if (isset($userAddress)) {
                if ($userAddress->user_id == Auth::id()) {
                    $response = ['success' => false, 'message' => __('You can not give coin to your own address')];
                    return $response;
                }
                $myWallet = Wallet::where(['id' => $request->wallet_id])->first();
                if (isset($myWallet)) {
                    $userWallet = get_primary_wallet($userAddress->user_id, $myWallet->coin_type);
                    $fees = check_withdrawal_fees($request->amount, $userAddress->withdrawal_fees);
                    if ($myWallet->balance < ($request->amount + $fees)) {
                        $response = ['success' => false, 'message' => __('Your selected wallet has not enough coin to give')];
                        return $response;
                    }
                    $data = [
                        'amount' => $request->amount,
                        'receiver_wallet_id' => $userWallet->id,
                        'sender_wallet_id' => $myWallet->id,
                        'receiver_user_id' => $userAddress->user_id,
                        'sender_user_id' => Auth::id(),
                        'fees' => $fees,
                        'update_id' => ''
                    ];

//                    $this->sendCoinToUser($data);
                    dispatch(new GiveCoin($data))->onQueue('give-coin');

                    $response = ['success' => true, 'message' => __('Coin sent successfully.')];
                } else {
                    $response = ['success' => false, 'message' => __('Wallet not found')];
                }

            } else {
                $response = ['success' => false, 'message' => __('User not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // accept coin request process
    public function acceptCoinRequest($request_id)
    {
        try {
            $request_coin = CoinRequest::where(['id' => $request_id, 'status'=> STATUS_PENDING])->first();

            if (isset($request_coin)) {
                $user = User::where(['id'=> $request_coin->sender_user_id])->first();

                $myWallet = Wallet::where(['id' => $request_coin->sender_wallet_id])->first();
                $userWallet = Wallet::where(['id' => $request_coin->receiver_wallet_id])->first();
                $amount = $request_coin->amount + $request_coin->fees;
                if (isset($myWallet)) {
                    if ($myWallet->balance < $amount) {
                        $response = ['success' => false, 'message' => __('Your wallet has not enough coin to give')];
                        return $response;
                    }
                    $data = [
                        'amount' => $request_coin->amount,
                        'receiver_wallet_id' => $request_coin->receiver_wallet_id,
                        'sender_wallet_id' => $request_coin->sender_wallet_id,
                        'receiver_user_id' => $request_coin->receiver_user_id,
                        'sender_user_id' => $request_coin->sender_user_id,
                        'update_id' => $request_coin->id,
                        'fees' => $request_coin->fees
                    ];

//                    $this->sendCoinToUser($data);
                    dispatch(new GiveCoin($data))->onQueue('give-coin');

                    $response = ['success' => true, 'message' => __('Coin request accepted successfully.')];
                } else {
                    $response = ['success' => false, 'message' => __('Wallet not found')];
                }

            } else {
                $response = ['success' => false, 'message' => __('Request not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }
//  coin request rejected process
    public function rejectCoinRequest($request_id)
    {
        try {
            $request_coin = CoinRequest::where(['id' => $request_id, 'status'=> STATUS_PENDING])->first();

            if (isset($request_coin)) {
                $request_coin->update(['status'=> STATUS_REJECTED]);

                $response = ['success' => true, 'message' => __('Coin request rejected successfully.')];
            } else {
                $response = ['success' => false, 'message' => __('Request not found')];
            }

        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }

        return $response;
    }

    // send coin to user
    public function sendCoinToUser($data)
    {
        DB::beginTransaction();
        try {
            Log::info('give coin process started');
            $myWallet = Wallet::where(['id' => $data['sender_wallet_id']])->first();
            $userWallet = Wallet::where(['id' => $data['receiver_wallet_id']])->first();
            if ($myWallet->balance < $data['amount'] + $data['fees']) {
                $response = ['success' => false, 'message' => __('Your selected wallet has not enough coin to give')];
                Log::info('Your selected wallet has not enough coin to give');
                Log::info('give coin process failed');

                return $response;
            }
            $amount = $data['amount'] - $data['fees'];
            if (!empty($data['update_id'])) {
                CoinRequest::where('id',$data['update_id'])->update(['status' => STATUS_SUCCESS]);
                Log::info('give coin = '.$data['amount']);
                Log::info('sender wallet id = '.$data['sender_wallet_id']);
                Log::info('receiver wallet id = '.$data['receiver_wallet_id']);

                $myWallet->decrement('balance', $amount);
                $userWallet->increment('balance',$amount);
            } else {
                $save = CoinRequest::create($data);
                if ($save) {
                    CoinRequest::where('id',$save->id)->update(['status' => STATUS_SUCCESS]);
                    Log::info('give coin = '.$data['amount']);
                    Log::info('sender wallet id = '.$data['sender_wallet_id']);
                    Log::info('receiver wallet id = '.$data['receiver_wallet_id']);

                    $myWallet->decrement('balance',$amount);
                    $userWallet->increment('balance',$amount);
                }
            }
            DB::commit();

            Log::info('give coin process success');
            $response = ['success' => true, 'message' => __('Coin sent successfully.')];

        } catch (\Exception $e) {
            Log::info('give coin process exception');
            Log::info($e->getMessage());
            DB::rollBack();
            $response = ['success' => false, 'message' => __('Something went wrong')];
            return $response;
        }
    }


    // buy coin with coin payment
    public function buyCoinWithCoinPayment($request, $coin_amount, $coin_price_doller,$phase_id,$referral_level, $phase_fees, $bonus, $affiliation_percentage)
    {
        $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];

        DB::beginTransaction();
        try {
            $response['data'] = (object)[];
            $response['success'] = false;
            $response['message'] = __('Invalid operation');

            $coin_payment = new CoinPaymentsAPI();

            $coin_type = isset($request->payment_coin_type) ? $request->payment_coin_type : allsetting('base_coin_type');
            $address = $coin_payment->GetCallbackAddress($coin_type);

            if ( isset($address['error']) && ($address['error'] == 'ok') ) {

                $api_rate = $coin_payment->GetRates('');
                $coin_price_btc = converts_currency($coin_price_doller, $coin_type,$api_rate);

                if ( $address ) {
                    if (isset($coin_price_btc) && $coin_price_btc > 0) {
                        $btc_transaction = new BuyCoinHistory();
                        $btc_transaction->address = $address['result']['address'];
                        $btc_transaction->type = BTC;
                        $btc_transaction->user_id = Auth::id();
                        $btc_transaction->phase_id = $phase_id;
                        $btc_transaction->referral_level = $referral_level;
                        $btc_transaction->fees  = $phase_fees ;
                        $btc_transaction->bonus = $bonus;
                        $btc_transaction->referral_bonus = $affiliation_percentage;
                        $btc_transaction->requested_amount = $coin_amount;
                        $btc_transaction->coin = $request->coin;
                        $btc_transaction->doller = $coin_price_doller;
                        $btc_transaction->btc = $coin_price_btc;
                        $btc_transaction->coin_type = $coin_type;
                        $btc_transaction->save();

                        $response['data'] = $btc_transaction;
                        $response['success'] = true;
                        $response['message'] = __('Order placed successfully');

                        DB::commit();
                    } else {
                        $response['data'] = (object)[];
                        $response['success'] = false;
                        $response['message'] = __('Coin payment not working properly');
                    }
                } else {
                    $response['data'] = (object)[];
                    $response['success'] = false;
                    $response['message'] = __('Coin payment address not generated');
                }
            } else {
                $response['data'] = (object)[];
                $response['success'] = false;
                $response['message'] = __('Coin payment not working properly');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('buy coin with coin payment exception '.$e->getMessage());
            $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];
        }

        return $response;
    }



    // buy coin with bank transfer
    public function buyCoinWithBank($request, $coin_amount, $coin_price_doller, $coin_price_btc, $phase_id, $referral_level, $phase_fees, $bonus, $affiliation_percentage)
    {
        $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];

        DB::beginTransaction();
        try {
            $bank = Bank::where(['status' => STATUS_ACTIVE, 'id' => $request->bank_id])->first();
            if (isset($bank)) {
                $btc_transaction = new BuyCoinHistory();
                $btc_transaction->type = BANK_DEPOSIT;
                $btc_transaction->address = 'N/A';
                $btc_transaction->user_id = Auth::id();
                $btc_transaction->doller = $coin_price_doller;
                $btc_transaction->btc = $coin_price_btc;
                $btc_transaction->phase_id = $phase_id;
                $btc_transaction->referral_level = $referral_level;
                $btc_transaction->fees  = $phase_fees ;
                $btc_transaction->bonus = $bonus;
                $btc_transaction->referral_bonus = $affiliation_percentage;
                $btc_transaction->requested_amount = $coin_amount;
                $btc_transaction->coin = $request->coin;
                $btc_transaction->coin_type = "BTC";
                $btc_transaction->bank_id = $request->bank_id;
                $btc_transaction->bank_sleep = uploadFile($request->file('sleep'), IMG_SLEEP_PATH);
                $btc_transaction->save();

                DB::commit();

                $response = ['success' => true, 'message' => __('Request submitted successful,Please wait for admin approval.'), 'data' => $btc_transaction];
            } else {
                $response = ['success' => false, 'message' => __('No active bank found'), 'data' => (object)[]];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('buy coin with bank exception '.$e->getMessage());
            $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];
        }

        return $response;
    }


    // buy coin with stripe

    public function buyCoinWithStripe($request, $coin_amount, $coin_price_doller, $coin_price_btc, $phase_id, $referral_level, $phase_fees, $bonus, $affiliation_percentage)
    {
        $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];

        try {
            $stripe_secret = '';
            if (!empty(settings()['STRIPE_SECRET'])) {
                $stripe_secret = settings()['STRIPE_SECRET'];
            }

            Stripe::setApiKey($stripe_secret);

            $charge = Charge::create ([
                'shipping'=>[
                    "name"=>"Jhon dhoe",
                    "address"=>[
                        "city"=>$request->city,
                        "country"=>$request->country,
                        "line1"=>$request->billing_address
                    ]
                ],
                "amount" => $coin_price_doller * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Payment from ".Auth::user()->email. ' for '.$coin_price_doller. ' usd'
            ]);

        } catch (\Exception $e) {

            $response = ['success' => false, 'message' => $e->getMessage(), 'data' => (object)[]];
            return $response;
        }
        DB::beginTransaction();
        try {
            if (isset($charge) && $charge['status'] == 'succeeded') {
                $btc_transaction = new BuyCoinHistory();
                $btc_transaction->type = STRIPE;
                $btc_transaction->address = $request->billing_address;
                $btc_transaction->user_id = Auth::id();
                $btc_transaction->doller = $coin_price_doller;
                $btc_transaction->btc = $coin_price_btc;
                $btc_transaction->phase_id = $phase_id;
                $btc_transaction->referral_level = $referral_level;
                $btc_transaction->fees  = $phase_fees ;
                $btc_transaction->bonus = $bonus;
                $btc_transaction->referral_bonus = $affiliation_percentage;
                $btc_transaction->requested_amount = $coin_amount;
                $btc_transaction->coin = $request->coin;
                $btc_transaction->coin_type = 'BTC';
                $btc_transaction->stripe_token = $charge['id'];
                $btc_transaction->save();

                DB::commit();
                $response = ['success' => true, 'message' =>  __("Request submitted successful,Please wait for admin approval"), 'data' => $btc_transaction];
            } else {
                $response = ['success' => false, 'message' =>  __("Payment failed"), 'data' => (object)[]];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('buy coin with stripe exception '.$e->getMessage());
            $response = ['success' => false, 'message' => __('Something went wrong'), 'data' => (object)[]];
        }

        return $response;
    }

}
