<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Services\CommonService;
use App\Model\BuyCoinHistory;
use App\Model\DepositeTransaction;
use App\Model\Notification;
use App\Model\ReferralUser;
use App\Model\Wallet;
use App\Model\WalletAddressHistory;
use App\Repository\AffiliateRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;
use Pusher\PusherException;

class WalletNotifier extends Controller
{
    // Wallet notifier for checking and confirming order process
    public function coinPaymentNotifier(Request $request)
    {
        Log::info('payment notifier called');
        $raw_request = $request->all();
        Log::info(json_encode($raw_request));
        $merchant_id = settings('ipn_merchant_id');
        $secret = settings('ipn_secret');

        Log::info('merchant_id =>'.$merchant_id);
        Log::info('ipn_secret =>'.$secret);

        if (env('APP_ENV') != "local"){
            if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
                Log::info('No HMAC signature sent');

                die("No HMAC signature sent");
            }

            $merchant = isset($_POST['merchant']) ? $_POST['merchant']:'';
            if (empty($merchant)) {
                Log::info('No Merchant ID passed');

                die("No Merchant ID passed");
            }

            if ($merchant != $merchant_id) {
                Log::info('Invalid Merchant ID');

                die("Invalid Merchant ID");
            }

            $request = file_get_contents('php://input');
            if ($request === FALSE || empty($request)) {
                Log::info('Error reading POST data');

                die("Error reading POST data");
            }

            $hmac = hash_hmac("sha512", $request, $secret);

            if ($hmac != $_SERVER['HTTP_HMAC']) {
                Log::info('HMAC signature does not match');

                die("HMAC signature does not match");
            }
        }

        return $this->depositeWallet($raw_request);
    }

    public function depositeWallet($request)
    {
        Log::info('call deposit wallet');
        $data = ['success'=>false,'message'=>'something went wrong'];

        DB::beginTransaction();
        try {
            $request = (object)$request;
            Log::info(json_encode($request));

            $walletAddress = WalletAddressHistory::where(['address'=> $request->address])->with('wallet')->first();

            if (isset($walletAddress)) {
                if (($request->ipn_type == "deposit") && ($request->status >= 100)) {
                    $wallet =  $walletAddress->wallet;
                    $data['user_id'] = $wallet->user_id;
                    if (!empty($wallet)){
                        $checkDeposit = DepositeTransaction::where('transaction_id', $request->txn_id)->first();
                        if (isset($checkDeposit)) {
                            $data = ['success'=>false,'message'=>'Transaction id already exists in deposit'];
                            Log::info('Transaction id already exists in deposit');
                            return $data;
                        }
                        $coinAmount = convert_to_crypt($request->amount, $walletAddress->coin_type);
                        $depositData = [
                            'address' => $request->address,
                            'address_type' => ADDRESS_TYPE_EXTERNAL,
                            'amount' => $request->amount,
                            'fees' => 0,
                            'doller' => $coinAmount * settings('coin_price'),
                            'btc' => $request->amount,
                            'type' => $walletAddress->coin_type,
                            'transaction_id' => $request->txn_id,
                            'confirmations' => $request->confirms,
                            'status' => STATUS_SUCCESS,
                            'receiver_wallet_id' => $wallet->id
                        ];

                        $depositCreate = DepositeTransaction::create($depositData);
                        Log::info(json_encode($depositCreate));

                        if (($depositCreate)) {

                            Notification::create(['user_id'=>$wallet->user_id, 'title'=>$walletAddress->coin_type." deposited", 'notification_body'=>$request->amount.$walletAddress->coin_type." deposited successfully"]);


                            Log::info('Balance before deposit '.$wallet->balance);
                            $wallet->increment('balance', $depositCreate->amount);
                            Log::info('Balance after deposit '.$wallet->balance);
                            $data['message'] = 'Deposit successfully';
                            $data['success'] = true;
                        } else {
                            Log::info('Deposit not created ');
                            $data['message'] = 'Deposit not created';
                            $data['success'] = false;
                        }

                    } else {
                        $data = ['success'=>false,'message'=>'No wallet found'];
                        Log::info('No wallet found');
                    }
                }
            } else {
                $data = ['success'=>false,'message'=>'Wallet address not found'];
                Log::info('Wallet address not found id db trying to buy coin');

                $buy_coin = BuyCoinHistory::where("address",$request->address)->where("btc","<=",$request->amount)->where("status",STATUS_PENDING)->first();
                if (!empty($buy_coin)){
                    $buy_coin->status = STATUS_SUCCESS;
                    $buy_coin->save();

                    $affiliate_servcice = new AffiliateRepository();
                    $primary = get_primary_wallet($buy_coin->user_id, 'Default');
                    $primary->increment('balance', $buy_coin->coin);

                    $referral = ReferralUser::where("user_id", $buy_coin->user_id)->first();

                    if (!empty($referral)){
                        $signUpBonus = isset(allsetting()['referral_signup_reward']) ? allsetting()['referral_signup_reward'] : 0;
                        $commonService = new CommonService();
                        $commonService->referralBonus($referral,$buy_coin->coin,REFERRAL_BONUS_BUY);
                    }
                    Notification::create(['user_id'=>$buy_coin->user_id, 'title'=>allsetting('coin_name')." deposited", 'notification_body'=>$buy_coin->coin." ".allsetting('coin_name')." deposited successfully"]);


                }else{
                    Log::info('Buy history not found');
                }

            }

            DB::commit();
            return $data;
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage().' '.$e->getLine();
            Log::info($data['message']);
            DB::rollback();

            return $data;
        }



    }


    /**
     * For broadcast data
     * @param $data
     */
    public function broadCast($data)
    {
        $channelName = 'depositConfirmation.' . customEncrypt($data['userId']);
        $fields = json_encode([
            'channel_name' => $channelName,
            'event_name' => 'confirm',
            'broadcast_data' => $data['broadcastData'],
        ]);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://' . env('BROADCAST_HOST') . '/api/broadcast',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                'broadcast-secret: an9$md_eoUqmNpa@bm34Jd'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
    }


}
