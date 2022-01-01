@extends('auth.master',['menu'=>'dashboard'])
@section('title', isset($title) ? $title : '')

@section('content')
    <div class="form-top">

        <p>{{__('Forgot Password')}}</p>
    </div>
    {{Form::open(['route' => 'sendForgotMail', 'files' => true])}}
    <div class="form-group">
        <label>{{__('Email address')}}</label>
        <input type="email" name="email" class="form-control" placeholder="{{__('Your email here')}}">
    </div>
    <button type="submit" class="btn btn-primary nimmu-user-sibmit-button">{{__('Send')}}</button>
    {{ Form::close() }}
    <div class="form-bottom text-center">
        <p>{{__('Return to sign in')}} <a href="{{route('login')}}">{{__('Sign in')}}</a></p>
    </div>
@endsection

@section('script')
@endsection
