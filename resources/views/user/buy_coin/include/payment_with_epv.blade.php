<div class="card-body">
    <div class="cp-user-card-header-area">
        <h4>{{__('Payment with EPV ')}}</h4>
    </div>

    <div class="cp-user-buy-coin-content-area mt-5">
        <div class="cp-user-coin-info">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="cp-user-card-header-area">
                        <h4>{{__('Before complete payment you must login to EPV account ')}}</h4>
                    </div>
                    <form action="{{ route('LoginWithEpv') }}" method="POST" enctype="multipart/form-data" id="">
                        @csrf
                        <div class="form-group">
                            <label>{{__('Email')}}</label>
                            <input name="email" type="email" autocomplete="off" id="" class="form-control" placeholder="{{__('EPV User Email')}}">
                        </div>
                        <div class="form-group">
                            <label>{{__('Password')}}</label>
                            <input name="password" type="password" autocomplete="off" id="" class="form-control" placeholder="{{__('EPV User Password')}}">
                        </div>
                        <button id="buy_button" type="submit" class="btn normal-btn theme-btn">{{__('Login')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
