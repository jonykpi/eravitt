<?php

namespace App\Http\Controllers\user;

use App\Http\Requests\checkEpvBalanceRequest;
use App\Http\Requests\EpvLoginRequest;
use App\Http\Requests\EpvPaymentRequest;
use App\Model\BuyCoinHistory;
use App\Services\EPVPaymentApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EPVController extends Controller
{
    private $api;
    public function __construct()
    {
        $this->api = new EPVPaymentApiService();
    }
    // login with epv
    public function LoginWithEpv(EpvLoginRequest $request)
    {
        try {
            $params = ['email' => $request->email, 'password' => $request->password];

            $response = $this->api->evpLogin($params);
            dd($response);
            if($response->status == 200) {
                Cookie::queue('security_pin', $response->security_pin);
                Cookie::queue('evp_ledger', $response->evp_ledger);
                Cookie::queue('epv_user_id', $response->user_id);
                $data = [
                    "user_id" => $response->user_id,
                    "name" => $response->name,
                    "email"=> $response->email,
                    "phone"=> $response->phone,
                    "evp_ledger" => $response->evp_ledger,
                    "token" => $response->token
                ];

                return redirect()->route('confirmPaymentWithEpv',$data)->with('success', $response->message);
            } else {
                return redirect()->back()->with('dismiss', $response->message);
            }
        } catch (\Exception $e) {
            Log::info('LoginWithEpv exception '.$e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

    public function confirmPaymentWithEpv(Request $request)
    {
        $data['data'] = $request->all();
        $data['requested_amount'] = Cookie::get('requestedAmount');

        return view('user.buy_coin.confirm_epv_payment',$data);
    }

    // check amount and security pin
    public function checkEpvBalance(checkEpvBalanceRequest $request)
    {
        $evp_ledger = Cookie::get('evp_ledger');
        Cookie::queue('requestedAmount', $request->requested_amount);
        if ($request->requested_amount > $evp_ledger) {
            return redirect()->back()->with('dismiss', __('Insufficient balance'));
        }

        return redirect()->route('confirmPaymentWithEpvProcess')->with('success', __("Input the otp and confirm the payment"));
    }

    public function confirmPaymentWithEpvProcess(Request $request)
    {
        $data['epv_user_id'] = Cookie::get('epv_user_id');
        $data['amount'] = Cookie::get('requestedAmount');

        return view('user.buy_coin.payment_confirm_epv', $data);
    }
    // confirm payment

    public function confirmPaymentProcessWithEpv(EpvPaymentRequest $request)
    {
        DB::beginTransaction();
        try {
            $pin = Cookie::get('security_pin');
            if ($pin != $request->security_pin) {
                return redirect()->back()->with('dismiss', __('Invalid otp. try again'));
            }
            $params = ['user_id' => $request->user_id, 'req_evp_ledger' => $request->requested_amount];
            $btc_transaction = new BuyCoinHistory();
            $btc_transaction->type = EPV;
            $btc_transaction->user_id = Auth::id();
            $btc_transaction->requested_amount = $request->requested_amount;
            $btc_transaction->coin = $request->requested_amount;
            $btc_transaction->doller = $request->requested_amount * settings('coin_price');
            $btc_transaction->address = 'N/A';
            $btc_transaction->coin_type = DEFAULT_COIN_TYPE;
            $btc_transaction->save();

            $response = $this->api->epvCheckout($params);
            if($response->status == 200) {
                Log::info(json_encode($response));
                $primary = get_primary_wallet(Auth::id(), 'Default');

                $primary->increment('balance', $btc_transaction->coin);
                $btc_transaction->status = STATUS_SUCCESS;
                $btc_transaction->save();

                DB::commit();
                return redirect()->route('buyCoin')->with('success', $response->message);
            } else {
                DB::rollBack();
                return redirect()->back()->with('dismiss', $response->message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('confirmPaymentProcessWithEpv exception '.$e->getMessage());
            return redirect()->back()->with('dismiss', __('Something went wrong'));
        }
    }

}
