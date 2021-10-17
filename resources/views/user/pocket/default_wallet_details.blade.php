@extends('user.master',['menu'=>'pocket','sub_menu'=>'my_pocket'])
@section('title', isset($title) ? $title : '')
@section('style')
    <style>
        .address-pagin ul.pagination li.page-item:not(:last-child) {
            margin-right: 5px;
        }

        .address-pagin ul.pagination .page-item .page-link {
            color: #fff;
            background: transparent;
            border: none;
            font-size: 16px;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .address-pagin ul.pagination .page-item:hover .page-link,
        .address-pagin ul.pagination .page-item.active .page-link {
            background: linear-gradient(to bottom, #3e4c8d 0%, #4254a5 100%);
            border-radius: 2px;
        }
    </style>
@endsection
@section('content')
    <div class="card cp-user-custom-card cp-user-deposit-card">
        <div class="row">
            <div class="col-sm-12">
                <div class="wallet-inner">
                    <div class="wallet-content card-body">
                        <div class="wallet-top cp-user-card-header-area">
                            <div class="title">
                                <div class="wallet-title text-center">
                                    <h4>{{$wallet->name}}</h4>
                                </div>
                            </div>
                            <div class="tab-navbar">
                                <div class="tabe-menu">
                                    <ul class="nav cp-user-profile-nav mb-0" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link wallet {{($active == 'deposit') ? 'active' : ''}}"
                                               id="diposite-tab"
                                               href="{{route('walletDetails',$wallet->id)}}?q=deposit"
                                               aria-controls="diposite" aria-selected="true">
                                                <i class="flaticon-wallet"></i> {{__('Deposit')}}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link send  {{($active == 'withdraw') ? 'active' : ''}}"
                                               id="withdraw-tab"
                                               href="{{route('withdrawalCoin')}}"
                                               aria-controls="withdraw" aria-selected="false">
                                                <i class="flaticon-send"> </i> {{__('Withdraw')}}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link share  {{($active == 'activity') ? 'active' : ''}}"
                                               id="activity-tab"
                                               href="{{route('walletDetails',$wallet->id)}}?q=activity"
                                               aria-controls="activity" aria-selected="false">
                                                <i class="flaticon-share"> </i> {{__('Activity log')}}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade   {{($active == 'deposit') ? 'show active' : ''}} in"
                                 id="diposite" role="tabpanel"
                                 aria-labelledby="diposite-tab">
                                <div class="row">
                                    <div class="col-lg-4 offset-lg-1">
                                        <div class="form-area cp-user-profile-info withdraw-form">
                                        <div class="form-group d-none after_connect" id="amount">
                                            <input type="hidden" name="chain_id" id="chain_id" value="{{allsetting('chain_id')}}">
                                            <label for="amount">{{__('Amount')}}</label>
                                            <input name="amount" type="text" class="form-control" id="amount_input"
                                                   placeholder="Amount">
                                            <p class="text-warning" id="equ_btc"><span class="totalBTC"></span>
                                                <span class="coinType"></span></p>
                                        </div>
                                        <button onclick="connectWithMetamask()" type="button"
                                                class="btn profile-edit-btn before_connect">{{__('Confirm with metamask')}}
                                        </button>

                                            <button onclick="depositeFromMetamask()" type="button"
                                                    class="btn profile-edit-btn d-none after_connect">{{__('Pay with metamask')}}
                                            </button>

                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <h5 class="card-header">{{__("Metamask setup")}}</h5>
                                            <div class="card-body">
                                                <p>{{__('Download metamask from there')}} <a target="_blank" href="https://metamask.io/">{{__('Metamax')}}</a></p>
                                                <p>{{__('Add token to your metamask wallet')}}</p>
                                                <p> <label for="">{{__('Chain link')}} : </label></p>
                                                <p>
                                                    <label for="">{{allsetting('chain_link')}}</label>
                                                </p>
                                                <p>
                                                    <label for="">
                                                        {{__('Contract address')}} :
                                                    </label>
                                                </p>
                                                <p>
                                                    <label for="">
                                                        {{allsetting('contract_address')}}
                                                    </label>
                                                    <input type="hidden" id="contract_address" value="{{allsetting('contract_address')}}">
                                                    <input type="hidden" id="wallet_address" value="{{allsetting('wallet_address')}}">
                                                    <input type="hidden" id="callback_url" value="{{route('depositCallback')}}">
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade  {{($active == 'activity') ? 'show active' : ''}} in"
                                 id="activity" role="tabpanel" aria-labelledby="activity-tab">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="activity-area">
                                            <div class="activity-top-area">
                                                <div class="cp-user-card-header-area">
                                                    <div class="title">
                                                        <h4 id="list_title">{{__('All Deposit List')}}</h4>
                                                    </div>
                                                    <div class="deposite-tabs cp-user-deposit-card">
                                                        <div class="activity-right text-right">
                                                            <ul class="nav cp-user-profile-nav mb-0">
                                                                <li class="nav-item">
                                                                    <a class="nav-link @if(!isset($ac_tab)) active @endif"
                                                                       data-toggle="tab"
                                                                       onclick="$('#list_title').html('All Deposit List')"
                                                                       data-title=""
                                                                       href="#Deposit">{{__('Deposit')}}</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link @if(isset($ac_tab) && $ac_tab == 'withdraw') active @endif"
                                                                       data-toggle="tab"
                                                                       onclick="$('#list_title').html('All Withdrawal List')"
                                                                       href="#Withdraw">{{__('Withdraw')}}</a>
                                                                </li>
                                                                @if(co_wallet_feature_active() && $wallet->type == CO_WALLET)
                                                                    <li class="nav-item">
                                                                        <a class="nav-link @if(isset($ac_tab) && $ac_tab == 'co-withdraw') active @endif"
                                                                           data-toggle="tab"
                                                                           onclick="$('#list_title').html('Pending Multi-signature Withdrawals')"
                                                                           href="#co-withdraw">{{__('Pending Multi-signature Withdraw')}}</a>
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="activity-list">
                                                <div class="tab-content">
                                                    <div id="Deposit"
                                                         class="tab-pane fade @if(!isset($ac_tab)) show active @endif">

                                                        <div class="cp-user-wallet-table table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                <tr>
                                                                    <th>{{__('Address')}}</th>
                                                                    <th>{{__('Amount')}}</th>
                                                                    <th>{{__('Transaction Hash')}}</th>
                                                                    <th>{{__('Status')}}</th>
                                                                    <th>{{__('Created At')}}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(isset($histories[0]))
                                                                    @foreach($histories as $history)
                                                                        <tr>
                                                                            <td>{{$history->address}}</td>
                                                                            <td>{{$history->amount}}</td>
                                                                            <td>{{$history->transaction_id}}</td>
                                                                            <td>{{deposit_status($history->status)}}</td>
                                                                            <td>{{$history->created_at}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="5"
                                                                            class="text-center">{{__('No data available')}}</td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div id="Withdraw"
                                                         class="tab-pane fade @if(isset($ac_tab) && $ac_tab == 'withdraw') show active @endif ">

                                                        <div class="cp-user-wallet-table table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                <tr>
                                                                    <th>{{__('Address')}}</th>
                                                                    <th>{{__('Amount')}}</th>
                                                                    <th>{{__('Transaction Hash')}}</th>
                                                                    <th>{{__('Status')}}</th>
                                                                    <th>{{__('Created At')}}</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @if(isset($withdraws[0]))
                                                                    @foreach($withdraws as $withdraw)
                                                                        <tr>
                                                                            <td>{{$withdraw->address}}</td>
                                                                            <td>{{$withdraw->amount}}</td>
                                                                            <td>{{$withdraw->transaction_hash}}</td>
                                                                            <td>{{deposit_status($withdraw->status)}}</td>
                                                                            <td>{{$withdraw->created_at}}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="5"
                                                                            class="text-center">{{__('No data available')}}</td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    @if(co_wallet_feature_active() && $wallet->type == CO_WALLET)
                                                        <div id="co-withdraw"
                                                             class="tab-pane fade @if(isset($ac_tab) && $ac_tab == 'co-withdraw') show active @endif">

                                                            <div class="cp-user-wallet-table table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>{{__('Address')}}</th>
                                                                        <th>{{__('Amount')}}</th>
                                                                        <th>{{__('Status')}}</th>
                                                                        <th>{{__('Created At')}}</th>
                                                                        <th>{{__('Actions')}}</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @if(isset($tempWithdraws[0]))
                                                                        @foreach($tempWithdraws as $withdraw)
                                                                            <tr>
                                                                                <td>{{$withdraw->address}}</td>
                                                                                <td>{{$withdraw->amount}}</td>
                                                                                <td>{{__('Need co users approval')}}</td>
                                                                                <td>{{$withdraw->created_at}}</td>
                                                                                <td>
                                                                                    <ul class="d-flex justify-content-center align-items-center">
                                                                                        <li>
                                                                                            <a title="{{__('Approvals')}}"
                                                                                               href="{{route('coWalletApprovals', $withdraw->id)}}">
                                                                                                <img
                                                                                                    src="{{asset('assets/user/images/wallet-table-icons/send.svg')}}"
                                                                                                    class="img-fluid" alt="">
                                                                                            </a>
                                                                                        </li>
                                                                                        @if($withdraw->user_id == \Illuminate\Support\Facades\Auth::id())
                                                                                            <li>
                                                                                                <a title="{{__('Reject Withdraw')}}" class="confirm-modal"
                                                                                                   data-title="{{__('Do you really want to reject?')}}"
                                                                                                   href="javascript:" data-href="{{route('rejectCoWalletWithdraw', $withdraw->id)}}">
                                                                                                    <img style="width: 25px; opacity: 0.7"
                                                                                                         src="{{asset('assets/user/images/close.png')}}"
                                                                                                         class="img-fluid"
                                                                                                         alt="">
                                                                                                </a>
                                                                                            </li>
                                                                                        @endif
                                                                                    </ul>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="5"
                                                                                class="text-center">{{__('No data available')}}</td>
                                                                        </tr>
                                                                    @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bignumber.js/8.0.2/bignumber.min.js" integrity="sha512-7UzDjRNKHpQnkh1Wf1l6i/OPINS9P2DDzTwQNX79JxfbInCXGpgI1RPb3ZD+uTP3O5X7Ke4e0+cxt2TxV7n0qQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.5.1/web3.min.js" integrity="sha512-8Frac7ZdCMHBsKch6t/XEAKauXT1PXTgRGX/9NO3IzfLQ3QlTnr8ACRmJMOWPr3rxeCFpjUH+Hk7Y4v4zm825Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('js/abi.js')}}"></script>
    <script src="{{asset('js/chain.js')}}"></script>
@endsection
