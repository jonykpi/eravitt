<div class="card-body">
    <div class="cp-user-card-header-area">
        <h4>{{__('Request submitted successful,please send ')}} {{$coinAddress->btc.' '.$coinAddress->coin_type}} {{__(' with this address')}}</h4>
    </div>

    <div class="cp-user-buy-coin-content-area mt-5">
        <div class="cp-user-coin-info">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="qr-img text-center">
                        <div class="user-profile-area">
                            <div class="user-profile-img">
                                @if(isset($coinAddress->address))  {!! QrCode::size(300)->generate($coinAddress->address); !!} @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-6">
                    <div class="row no-gutters">
                        <div class="col-6 cp-user-card-header-area"><h4 class="font-weight-normal font-16">{{__('Address')}} </h4></div>
                        <div class="col-1 cp-user-card-header-area"><h4>:</h4></div>
                        <div class="col-5 px-1 cp-user-card-header-area"><h4 class="font-weight-normal font-16"> {{$coinAddress->address}} </h4></div>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-6 cp-user-card-header-area"><h4 class="font-weight-normal font-16">{{__('Payable Coin')}} </h4></div>
                        <div class="col-1 cp-user-card-header-area"><h4>:</h4></div>
                        <div class="col-5 px-1 cp-user-card-header-area"><h4 class="font-weight-normal font-16">{{$coinAddress->btc.' '.$coinAddress->coin_type}}</h4></div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <form action="{{ route('paymentConfirmInr') }}" method="POST" enctype="multipart/form-data" id="payInr">
                        @csrf
                        <input type="hidden" name="id" value="{{$coinAddress->id}}">

                        {{--<div class="form-group">--}}
                        {{--<label>{{__('Transaction ID')}}</label>--}}
                        {{--<input class="form-control" type="text" required name="transaction_id" value="">--}}
                        {{--</div>--}}
                        <button id="" type="submit" class="mt-5 btn normal-btn theme-btn">{{__('Paid')}}</button>
                        {{--<a href="{{route('buyCoin')}}" class="mt-5 btn normal-btn theme-btn">{{__('Pay later')}}</a>--}}


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