<div class="header-bar">
    <div class="table-title">
        <h3>{{__('General Settings')}}</h3>
    </div>
</div>
<div class="profile-info-form">
    <form action="{{route('adminCommonSettings')}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label>{{__('Language')}}</label>
                    <div class="cp-select-area">
                        <select name="lang" class="form-control">
                            @foreach(language() as $val)
                                <option
                                    @if(isset($settings['lang']) && $settings['lang']==$val) selected
                                    @endif value="{{$val}}">{{langName($val)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Coin Name')}}</label>
                    <input class="form-control" type="text" name="coin_name"
                           placeholder="{{__('Coin Name')}}" value="{{$settings['coin_name']}}">
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Company Name')}}</label>
                    <input class="form-control" type="text" name="company_name"
                           placeholder="{{__('Company Name')}}"
                           value="{{$settings['app_title']}}">
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Coin Price (in USD)')}} </label> <input type="checkbox" name="price_change" value="1">

                    <input class="form-control number_only" type="text" name="coin_price"
                           placeholder="{{__('coin price')}}"
                           value="{{isset($settings['coin_price']) ? $settings['coin_price'] : ''}}">
                </div>
            </div>

            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Company USD Account no.')}}</label>
                    <input class="form-control" type="text" name="admin_usdt_account_no"
                           placeholder="{{__('Company usd account mo.')}}"
                           value="{{isset($settings['admin_usdt_account_no']) ? $settings['admin_usdt_account_no'] : ''}}">
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Coin Payment Base Coin Type')}}</label>
                    <input class="form-control" type="text" name="base_coin_type"
                           placeholder="{{__('Coin Type eg. BTC')}}"
                           value="{{isset($settings['base_coin_type']) ? $settings['base_coin_type'] : ''}}">
                </div>
            </div>



            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Upi ids')}}</label>
                    <input class="form-control" type="text" name="upi_ids"
                           placeholder="{{__('upi ids')}}"
                           value="{{isset($settings['upi_ids']) ? $settings['upi_ids'] : ''}}">
                </div>
            </div>

            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('BTC address')}}</label>
                    <input class="form-control" type="text" name="btc_address"
                           placeholder="{{__('btc address')}}"
                           value="{{isset($settings['btc_address']) ? $settings['btc_address'] : ''}}">
                </div>
            </div>


            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Inr payment qr code text')}}</label>
                    <input class="form-control" type="text" name="inr_payment_qr_code_text"
                           placeholder="{{__('Text')}}"
                           value="{{isset($settings['inr_payment_qr_code_text']) ? $settings['inr_payment_qr_code_text'] : ''}}">
                </div>
            </div>


            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Bank details')}}</label>
                    <input class="form-control" type="text" name="bank_details_inr"
                           placeholder="{{__('Text')}}"
                           value="{{isset($settings['bank_details_inr']) ? $settings['bank_details_inr'] : ''}}">
                </div>
            </div>





            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Company USD Account no.')}}</label>
                    <input class="form-control" type="text" name="admin_usdt_account_no"
                           placeholder="{{__('Company usd account mo.')}}"
                           value="{{isset($settings['admin_usdt_account_no']) ? $settings['admin_usdt_account_no'] : ''}}">
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Coin Payment Base Coin Type')}}</label>
                    <input class="form-control" type="text" name="base_coin_type"
                           placeholder="{{__('Coin Type eg. BTC')}}"
                           value="{{isset($settings['base_coin_type']) ? $settings['base_coin_type'] : ''}}">
                </div>
            </div>
            <div class="col-lg-6 col-12  mt-20">
                <div class="form-group">
                    <label for="#">{{__('Copyright Text')}}</label>
                    <input class="form-control" type="text" name="copyright_text"
                           placeholder="{{__('Copyright Text')}}"
                           value="{{$settings['copyright_text']}}">
                </div>
            </div>
            <div class="col-lg-6 col-12 mt-20">
                <div class="form-group">
                    <label
                        for="#">{{__('Number of confirmation for Notifier deposit')}} </label>
                    <input class="form-control number_only" type="text"
                           name="number_of_confirmation" placeholder=""
                           value="{{$settings['number_of_confirmation']}}">
                </div>
            </div>
        </div>
        <div class="uplode-img-list">
            <div class="row">
                <div class="col-lg-4 mt-20">
                    <div class="single-uplode">
                        <div class="uplode-catagory">
                            <span>{{__('Logo')}}</span>
                        </div>
                        <div class="form-group buy_coin_address_input ">
                            <div id="file-upload" class="section-p">
                                <input type="file" placeholder="0.00" name="logo" value=""
                                       id="file" ref="file" class="dropify"
                                       @if(isset($settings['logo']) && (!empty($settings['logo'])))  data-default-file="{{asset(path_image().$settings['logo'])}}" @endif />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-20">
                    <div class="single-uplode">
                        <div class="uplode-catagory">
                            <span>{{__('Login Sidebar image')}}</span>
                        </div>
                        <div class="form-group buy_coin_address_input ">
                            <div id="file-upload" class="section-p">
                                <input type="file" placeholder="0.00" name="login_logo" value=""
                                       id="file" ref="file" class="dropify"
                                       @if(isset($settings['login_logo']) && (!empty($settings['login_logo'])))  data-default-file="{{asset(path_image().$settings['login_logo'])}}" @endif />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mt-20">
                    <div class="single-uplode">
                        <div class="uplode-catagory">
                            <span>{{__('Favicon')}}</span>
                        </div>
                        <div class="form-group buy_coin_address_input ">
                            <div id="file-upload" class="section-p">
                                <input type="file" placeholder="0.00" name="favicon" value=""
                                       id="file" ref="file" class="dropify"
                                       @if(isset($settings['favicon']) && (!empty($settings['favicon'])))  data-default-file="{{asset(path_image().$settings['favicon'])}}" @endif />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @if(isset($itech))
                <input type="hidden" name="itech" value="{{$itech}}">
            @endif
            <div class="col-lg-2 col-12 mt-20">
                <button class="button-primary theme-btn">{{__('Update')}}</button>
            </div>
        </div>
    </form>
</div>
