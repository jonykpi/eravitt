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
                                    @include('user.buy_coin.include.confirm_evp.evp_login_data')
                                </div>
                                <div class="col-lg-6">
                                    <div class="cp-user-card-header-area">
                                        <h4>{{__('Review the wallet info, then if you agree please input the pin here and confirm the payment')}}</h4>
                                    </div>

                                    <form action="{{ route('checkEpvBalance') }}" method="POST" enctype="multipart/form-data" id="">
                                        @csrf
{{--                                        <div class="form-group">--}}
{{--                                            <label>{{__('Security Pin')}}</label>--}}
{{--                                            <input name="security_pin" type="password" autocomplete="off" id="" class="form-control" placeholder="{{__('Security Pin')}}">--}}
{{--                                        </div>--}}
                                        <br>
{{--                                        <input type="hidden" name="user_id" value="{{$data['user_id']}}">--}}

                                        <div class="form-group">
                                            <label>{{__('Amount')}}</label>
                                        <input class="form-control" type="text" name="requested_amount" value="{{$requested_amount}}">
                                        </div>
                                        <button id="" type="submit" class="btn normal-btn theme-btn">{{__('Confirm')}}</button>
                                        <a href="{{route('buyCoin')}}" class="btn normal-btn btn-warning">{{__('Cancel')}}</a>
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
