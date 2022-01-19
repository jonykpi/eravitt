@extends('user.master',['menu'=>'buy_coin'])
@section('title', isset($title) ? $title : '')
@section('style')
    <style>
        .row-text{
            color: #fff;
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card cp-user-custom-card">
                <div class="card-body">
                    <div class="cp-user-card-header-area">
                        <h4>{{__('Confirm your payment ')}}</h4>
                    </div>

                    <div class="cp-user-buy-coin-content-area mt-5">
                        <div class="cp-user-coin-info">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <div class="cp-user-card-header-area">
                                        <h4>{{__('Input the OTP here and confirm the payment')}}</h4>
                                    </div>

                                    <form action="{{ route('confirmPaymentProcessWithEpv') }}" method="POST" enctype="multipart/form-data" id="">
                                        @csrf
                                        <input type="hidden" name="requested_amount" value="{{$amount}}">
                                        <div class="form-group">
                                            <label>{{__('Security Pin')}}</label>
                                            <input name="security_pin" type="password" autocomplete="off" id="" class="form-control" placeholder="{{__('Security Pin')}}">
                                        </div>
                                        <br>
                                        <input type="hidden" name="user_id" value="{{$evp_user_id}}">
                                        <button id="" type="submit" class="btn normal-btn theme-btn">{{__('Confirm')}}</button>
                                    </form>
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

@endsection
