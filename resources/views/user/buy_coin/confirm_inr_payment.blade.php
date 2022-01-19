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

                                <div class="col-lg-6 offset-lg-3">
                                    <div class="cp-user-card-header-area">

                                        <h4>Total payment amount : {{number_format($item->inr)}} <i class="fa fa-rupee"></i> and You will receive : {{number_format($item->coin)}} EVP</h4>

                                    </div>
 <div class="cp-user-card-header-area">


                                        <h4>{{__('Review the wallet info, then if you agree please input the transaction id here and confirm the payment')}}</h4>
                                    </div>

                                    <form action="{{ route('paymentConfirmInr') }}" method="POST" enctype="multipart/form-data" id="payInr">
                                        {!! QrCode::size(300)->generate(allsetting("inr_payment_qr_code_text")) !!}
                                        @csrf
{{--                                        <div class="form-group">--}}
{{--                                            <label>{{__('Security Pin')}}</label>--}}
{{--                                            <input name="security_pin" type="password" autocomplete="off" id="" class="form-control" placeholder="{{__('Security Pin')}}">--}}
{{--                                        </div>--}}
                                        <br>
{{--                                        <input type="hidden" name="user_id" value="{{$data['user_id']}}">--}}

                                        <div class="form-group mt-2">
                                            <h3 class="text-white">{{settings("bank_details_inr")}}</h3>
                                        </div>

                                        <div class="form-group mt-2">
                                            <img style="width: 306px; height: 77px;" src="{{asset("assets/img/PhonePePayTMGPay (2).png")}}" alt="">
                                            <h3  class="dd text-white mt-2">{{settings("upi_ids")}}</h3>
                                        </div>
                                        <input type="hidden" name="id" value="{{$item->id}}">

                                        {{--<div class="form-group">--}}
                                            {{--<label>{{__('Transaction ID')}}</label>--}}
                                        {{--<input class="form-control" type="text" required name="transaction_id" value="">--}}
                                        {{--</div>--}}
                                        <button id="" type="submit" class="mt-5 btn normal-btn theme-btn">{{__('Paid')}}</button>
                                        <a href="{{route('buyCoin')}}" class="mt-5 btn normal-btn theme-btn">{{__('Pay later')}}</a>


                                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Transaction ID</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input style="height: 40px;color: #213575;" class="form-control" name="transaction_id" id="transaction_id"></input>
                                                    </div>
                                                    <div class="modal-footer">
                                                        {{--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>--}}
                                                        <button type="submit" class="btn normal-btn theme-btn">Confirm</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <script>
        $("#payInr").on("submit",function (event) {
           if ( $("#transaction_id").val() == undefined || $("#transaction_id").val().length == 0){
               event.preventDefault();
               $("#exampleModal").modal("show");

           }


        })
    </script>

@endsection
