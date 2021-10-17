<?php

namespace App\Http\Controllers\admin;

use App\Http\Services\TransactionService;
use App\Model\CoinRequest;
use App\Model\DepositeTransaction;
use App\Model\Wallet;
use App\Model\WithdrawHistory;
use App\Repository\AffiliateRepository;
use App\Services\CoinPaymentsAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use function foo\func;

class TransactionController extends Controller
{
    // all personal wallet list
    public function adminWalletList(Request $request)
    {
        $data['title'] = __('Pocket List');
        $data['sub_menu'] = __('personal');
        if($request->ajax()){
            $data['wallets'] = Wallet::join('users','users.id','=','wallets.user_id')->where(['wallets.type'=>PERSONAL_WALLET])
                ->select(
                    'wallets.name'
                    ,'wallets.coin_type'
                    ,'wallets.balance'
                    ,'wallets.referral_balance'
                    ,'wallets.created_at'
                    ,'users.first_name'
                    ,'users.last_name'
                    ,'users.email'
                );

            return datatables()->of($data['wallets'])
                ->addColumn('user_name',function ($item){return $item->first_name.' '.$item->last_name;})
                ->make(true);
        }

        return view('admin.wallet.index',$data);
    }

    // all co wallet list
    public function adminCoWallets(Request $request)
    {
        $data['title'] = __('Pocket List');
        $data['sub_menu'] = __('co');
        if($request->ajax()){
            $data['wallets'] = Wallet::where(['type'=>CO_WALLET]);

            return datatables()->of($data['wallets'])
                ->addColumn('actions',function ($item) {
                    $html = '<ul class="d-flex justify-content-center align-items-center">';
                    $html .= '<li>
                                <a title="'.__("Co Users").'"
                                   href="'.route('adminCoWalletUsers', $item->id).'">
                                    <img
                                        src="'.asset('assets/user/images/sidebar-icons/user.svg').'"
                                        class="img-fluid" alt="">
                                </a>
                            </li>';
                    $html .= '</ul>';
                    return $html;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.wallet.co_wallets',$data);
    }


    // co wallet users
    public function adminCoWalletUsers(Request $request)
    {
        $data['title'] = __('Co Pocket Users');
        $data['sub_menu'] = 'co';
        $data['wallet'] = $data['coWallet'] = Wallet::where(['id'=>$request->id, 'type'=>CO_WALLET])->first();
        if(empty($data['wallet'])) return back();
        $data['co_users'] = $data['wallet']->co_users;

        return view('admin.wallet.co_wallet_users',$data);
    }


    //admin Default Coin  transaction  history
    public function adminDefaultCoinTransactionHistory(Request $request)
    {
        $data['title'] = __('Transaction History');
        if ($request->ajax()) {
            $transaction = CoinRequest::select('*');

            return datatables()->of($transaction)
                ->addColumn('status', function ($dpst) {
                    return deposit_status($dpst->status);
                })
                ->addColumn('sender_user_id', function ($dpst) {
                    return isset($dpst->sender) ? $dpst->sender->first_name . ' ' . $dpst->sender->last_name : 'N/A';
                })
                ->addColumn('receiver_user_id', function ($dpst) {
                    return isset($dpst->receiver) ? $dpst->receiver->first_name . ' ' . $dpst->receiver->last_name : 'N/A';
                })
                ->make(true);
        }

        return view('admin.transaction.default-transaction', $data);
    }
    // transaction  history
    public function adminTransactionHistory(Request $request)
    {
        $data['title'] = __('Transaction History');
        if ($request->ajax()) {
            $deposit = DepositeTransaction::select('deposite_transactions.address'
                , 'deposite_transactions.amount'
                , 'deposite_transactions.fees'
                , 'deposite_transactions.transaction_id'
                , 'deposite_transactions.confirmations'
                , 'deposite_transactions.address_type as addr_type'
                , 'deposite_transactions.created_at'
                , 'deposite_transactions.sender_wallet_id'
                , 'deposite_transactions.receiver_wallet_id'
                , 'deposite_transactions.status'
                , 'deposite_transactions.type'
            )->orderBy('deposite_transactions.id', 'desc');

            return datatables()->of($deposit)
                ->addColumn('address_type', function ($dpst) {
                    if ($dpst->addr_type == 'internal_address') {
                        return 'External';
                    } else {
                        return addressType($dpst->addr_type);
                    }

                })
                ->addColumn('type', function ($dpst) {
                    return find_coin_type($dpst->type);
                })
                ->addColumn('status', function ($dpst) {
                    return deposit_status($dpst->status);
                })
                ->addColumn('sender', function ($dpst) {
                    if (!empty($dpst->senderWallet) && $dpst->senderWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$dpst->senderWallet->name;
                    else
                        return isset($dpst->senderWallet->user) ? $dpst->senderWallet->user->first_name . ' ' . $dpst->senderWallet->user->last_name : 'N/A';
                })
                ->addColumn('receiver', function ($dpst) {
                    if (!empty($dpst->receiverWallet) && $dpst->receiverWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$dpst->receiverWallet->name;
                    else
                        return isset($dpst->receiverWallet->user) ? $dpst->receiverWallet->user->first_name . ' ' . $dpst->receiverWallet->user->last_name : 'N/A';
                })
                ->make(true);
        }

        return view('admin.transaction.all-transaction', $data);
    }
    // withdrawal history
    public function adminWithdrawalHistory(Request $request)
    {
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select('withdraw_histories.address'
                    , 'withdraw_histories.amount'
                    , 'withdraw_histories.user_id'
                    , 'withdraw_histories.fees'
                    , 'withdraw_histories.transaction_hash'
                    , 'withdraw_histories.confirmations'
                    , 'withdraw_histories.address_type as addr_type'
                    , 'withdraw_histories.created_at'
                    , 'withdraw_histories.wallet_id'
                    , 'withdraw_histories.coin_type'
                    , 'withdraw_histories.receiver_wallet_id'
                    , 'withdraw_histories.status'
                )->orderBy('withdraw_histories.id', 'desc');
            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return find_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    if(!empty($wdrl->user)) $user = $wdrl->user;
                    else $user = isset($wdrl->senderWallet) ? $wdrl->senderWallet->user : null;
                    return isset($user) ? $user->first_name . ' ' . $user->last_name : 'N/A';
                })
                ->addColumn('receiver', function ($wdrl) {
                    if (!empty($wdrl->receiverWallet) && $wdrl->receiverWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$wdrl->receiverWallet->name;
                    else
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : 'N/A';
                })
                ->addColumn('status', function ($wdrl) {
                    return deposit_status($wdrl->status);
                })
                ->make(true);
        }

        return view('admin.transaction.all-transaction');
    }



    // pending withdrawal list
    public function adminPendingWithdrawal(Request $request)
    {
        $data['title'] = __('Pending Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.id',
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.user_id'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.coin_type'
                , 'withdraw_histories.receiver_wallet_id'
            )->where(['withdraw_histories.status' => STATUS_PENDING])
                ->orderBy('withdraw_histories.id', 'desc');

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return find_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    if(!empty($wdrl->user)) $user = $wdrl->user;
                    else $user = isset($wdrl->senderWallet) ? $wdrl->senderWallet->user : null;
                    return isset($user) ? $user->first_name . ' ' . $user->last_name : 'N/A';
                })
                ->addColumn('receiver', function ($wdrl) {
                    if (!empty($wdrl->receiverWallet) && $wdrl->receiverWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$wdrl->receiverWallet->name;
                    else
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : 'N/A';
                })
                ->addColumn('actions', function ($wdrl) {
                    $action = '<ul>';
                    if ($wdrl->coin_type == 'Default') {
                        $action .= default_accept_html('adminPendingWithdrawalAcceptProcess',encrypt($wdrl->id));
                    } else {
                        $action .= accept_html('adminAcceptPendingWithdrawal',encrypt($wdrl->id));
                    }
                    $action .= reject_html('adminRejectPendingWithdrawal',encrypt($wdrl->id));
                    $action .= '<ul>';

                    return $action;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('admin.transaction.pending-withdrawal', $data);
    }

    // rejected withdrawal list
    public function adminRejectedWithdrawal(Request $request)
    {
        $data['title'] = __('Rejected Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.user_id'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.coin_type'
                , 'withdraw_histories.receiver_wallet_id'
            )->where(['withdraw_histories.status' => STATUS_REJECTED])
                ->orderBy('withdraw_histories.id', 'desc');

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return find_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    if(!empty($wdrl->user)) $user = $wdrl->user;
                    else $user = isset($wdrl->senderWallet) ? $wdrl->senderWallet->user : null;
                    return isset($user) ? $user->first_name . ' ' . $user->last_name : 'N/A';
                })
                ->addColumn('receiver', function ($wdrl) {
                    if (!empty($wdrl->receiverWallet) && $wdrl->receiverWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$wdrl->receiverWallet->name;
                    else
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : 'N/A';
                })
                ->make(true);
        }

        return view('admin.transaction.pending-withdrawal', $data);
    }

    // active withdrawal list
    public function adminActiveWithdrawal(Request $request)
    {
        $data['title'] = __('Active Withdrawal');
        if ($request->ajax()) {
            $withdrawal = WithdrawHistory::select(
                'withdraw_histories.address'
                , 'withdraw_histories.amount'
                , 'withdraw_histories.user_id'
                , 'withdraw_histories.fees'
                , 'withdraw_histories.transaction_hash'
                , 'withdraw_histories.confirmations'
                , 'withdraw_histories.address_type as addr_type'
                , 'withdraw_histories.updated_at'
                , 'withdraw_histories.wallet_id'
                , 'withdraw_histories.coin_type'
                , 'withdraw_histories.receiver_wallet_id'
            )->where(['withdraw_histories.status' => STATUS_SUCCESS])
                ->orderBy('withdraw_histories.id', 'desc');

            return datatables()->of($withdrawal)
                ->addColumn('address_type', function ($wdrl) {
                    return addressType($wdrl->addr_type);
                })
                ->addColumn('coin_type', function ($wdrl) {
                    return find_coin_type($wdrl->coin_type);
                })
                ->addColumn('sender', function ($wdrl) {
                    if(!empty($wdrl->user)) $user = $wdrl->user;
                    else $user = isset($wdrl->senderWallet) ? $wdrl->senderWallet->user : null;
                    return isset($user) ? $user->first_name . ' ' . $user->last_name : 'N/A';
                })
                ->addColumn('receiver', function ($wdrl) {
                    if (!empty($wdrl->receiverWallet) && $wdrl->receiverWallet->type == CO_WALLET) return  'Multi-signature Pocket: '.$wdrl->receiverWallet->name;
                    else
                    return isset($wdrl->receiverWallet->user) ? $wdrl->receiverWallet->user->first_name . ' ' . $wdrl->receiverWallet->user->last_name : 'N/A';
                })
                ->make(true);
        }

        return view('admin.transaction.pending-withdrawal', $data);
    }

    // accept process of pending withdrawal
    public function adminAcceptPendingWithdrawal($id)
    {
        if (isset($id)) {
            try {
                $wdrl_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            $transaction = WithdrawHistory::with('wallet')->with('users')->where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();
            $affiliate_servcice = new AffiliateRepository();
            if (!empty($transaction)) {
                if ($transaction->address_type == ADDRESS_TYPE_INTERNAL) {

                    $deposit = DepositeTransaction::where(['transaction_id' =>$transaction->transaction_hash, 'address' => $transaction->address])->update(['status' => STATUS_SUCCESS]);

                    Wallet::where(['id' => $transaction->receiver_wallet_id])->increment('balance', $transaction->amount);
                    $transaction->status = STATUS_SUCCESS;
                    $transaction->save();

                    return redirect()->back()->with('success', 'Pending withdrawal accepted Successfully.');

                } elseif ($transaction->address_type == ADDRESS_TYPE_EXTERNAL) {
                    try {
                        $currency =  $transaction->coin_type;

                        $coinpayment = new CoinPaymentsAPI();
                        $api_rate = $coinpayment->GetRates('');

                        $dollerAmmount = bcmul($transaction->amount,settings()['coin_price'],8);
                        $btcAmmount = converts_currency($dollerAmmount, $currency,$api_rate);
                        $response = $coinpayment->CreateWithdrawal($transaction->amount,$currency,$transaction->address);

                        if (is_array($response) && isset($response['error']) && ($response['error'] == 'ok') ) {
                            $transaction->transaction_hash = $response['result']['id'];
                            $transaction->status = STATUS_SUCCESS;
                            $transaction->update();
                            $bonus = $affiliate_servcice->storeAffiliationHistory($transaction);
                            return redirect()->back()->with('success', __('Pending withdrawal accepted Successfully.'));

                        } else {
                            return redirect()->back()->with('dismiss', $response['error']);
                        }
                    } catch(\Exception $e) {
                        return redirect()->back()->with('dismiss', $response['error']);
                    }
                }
            }

            return redirect()->back()->with('dismiss', __('Something went wrong! Please try again!'));
        }
    }

    // pending withdrawal reject process
    public function adminRejectPendingWithdrawal($id)
    {
        if (isset($id)) {
            try {
                $wdrl_id = decrypt($id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            $transaction = WithdrawHistory::where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();

            if (!empty($transaction)) {
                if ($transaction->address_type == ADDRESS_TYPE_INTERNAL) {

                    Wallet::where(['id' => $transaction->wallet_id])->increment('balance', $transaction->amount);
                    $transaction->status = STATUS_REJECTED;
                    $transaction->update();

                    $deposit = DepositeTransaction::where(['transaction_id' =>$transaction->transaction_hash, 'address' => $transaction->address])->update(['status' => STATUS_REJECTED]);

                    return redirect()->back()->with('success', 'Pending withdrawal rejected Successfully.');
                } elseif ($transaction->address_type == ADDRESS_TYPE_EXTERNAL) {
                    Wallet::where(['id' => $transaction->wallet_id])->increment('balance', $transaction->amount);
                    $transaction->status = STATUS_REJECTED;

                    $transaction->update();

                    return redirect()->back()->with('success', __('Pending Withdrawal rejected Successfully.'));
                }
            }

            return redirect()->back()->with('dismiss', __('Something went wrong! Please try again!'));
        }
    }

    // admin accept pending withdrawal
    public function adminPendingWithdrawalAcceptProcess(Request $request)
    {
        if (isset($request->withdrawal_id)) {
            if (empty($request->transaction_hash)) {
                return redirect()->back()->with('dismiss', __('Transaction hash is required'));
            }
            try {
                $wdrl_id = decrypt($request->withdrawal_id);
            } catch (\Exception $e) {
                return redirect()->back();
            }
            $transaction = WithdrawHistory::with('wallet')->with('users')->where(['id' => $wdrl_id, 'status' => STATUS_PENDING])->firstOrFail();
            if (!empty($transaction)) {
                try {
                    $transaction->transaction_hash = $request->transaction_hash;
                    $transaction->status = STATUS_SUCCESS;
                    $transaction->update();

                    return redirect()->back()->with('success', __('Pending withdrawal accepted Successfully.'));

                } catch(\Exception $e) {
                    return redirect()->back()->with('dismiss', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('dismiss', __('Transaction not found'));
            }
        } else {
            return redirect()->back()->with('dismiss', __('Withdrawal id not found'));
        }
    }
}
