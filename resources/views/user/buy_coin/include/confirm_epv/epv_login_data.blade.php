<div class="cp-user-card-header-area">
    <h4>{{__('Here is the wallet info')}}</h4>
</div>
<div class="row row-text">
    <div class="col-md-4">{{__('Name')}}</div>
    <div class="col-md-1">:</div>
    <div class="col-md-7">{{ $data['name'] }}</div>
</div>
<div class="row row-text">
    <div class="col-md-4">{{__('Email')}}</div>
    <div class="col-md-1">:</div>
    <div class="col-md-7">{{ $data['email'] }}</div>
</div>
<div class="row row-text">
    <div class="col-md-4">{{__('Current Balance')}}</div>
    <div class="col-md-1">:</div>
    <div class="col-md-7">{{ $data['evp_ledger'].' '.settings('coin_name') }}</div>
</div>
<div class="row row-text">
    <div class="col-md-4">{{__('Requested Amount')}}</div>
    <div class="col-md-1">:</div>
    <div class="col-md-7">{{ $requested_amount.' '.settings('coin_name') }}</div>
</div>
<div class="row row-text">
    <div class="col-md-4">{{__('After deduct the balance will')}}</div>
    <div class="col-md-1">:</div>
    <div class="col-md-7">{{ ($data['evp_ledger'] - $requested_amount).' '. settings('coin_name') }}</div>
</div>
