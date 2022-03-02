@extends('user.master',['menu'=>'buy_coin'])
@section('title', isset($title) ? $title : '')
@section('style')
    <style>
        .user-profile-img{
            height: auto !important;
            width: auto !important;
            border-radius: 0% !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card cp-user-custom-card">
                @if($type == 'card')
                    @include('user.buy_coin.include.payment_with_card')
                @elseif($type == 'evp')
                    @include('user.buy_coin.include.payment_with_evp')
                @else
                    @include('user.buy_coin.include.payment_with_coin_payment')
                @endif
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection
