@extends('admin.master',['menu'=>'setting', 'sub_menu'=>'config'])
@section('title', isset($title) ? $title : '')
@section('style')
@endsection
@section('content')
    <!-- breadcrumb -->
    <div class="custom-breadcrumb">
        <div class="row">
            <div class="col-9">
                <ul>
                    <li>{{__('Setting')}}</li>
                    <li class="active-item">{{ $title }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->

    <!-- User Management -->
    <div class="user-management p-4">
        <div class="row">
            <div class="col-12">
                <div class="header-bar p-4">
                    <div class="table-title">
                        <h3>{{ $title }}</h3>
                    </div>
                </div>
                <div class="dashboard-status config-section">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-12 mb-xl-0 mb-4">
                            <div class="card status-card">
                                <div class="card-body py-0">
                                    <div class="status-card-inner">
                                        <div class="content">
                                            <p>{{__('Clear Application Cache')}}</p>
                                            <small>{{__('Clear all application cache by clicking this button and also you can run the cache clear command')}}</small>
                                            <a href="{{route('adminRunCommand',COMMAND_TYPE_CACHE)}}" class="theme-btn btn-success">{{__('Cache Clear')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-12 mb-xl-0 mb-4">
                            <div class="card status-card">
                                <div class="card-body py-0">
                                    <div class="status-card-inner">
                                        <div class="content">
                                            <p>{{__('Clear Application Config')}}</p>
                                            <small>{{__('Reset or clear all configuration by clicking this button and also you can run the config clear command')}}</small>
                                            <a href="{{route('adminRunCommand',COMMAND_TYPE_CONFIG)}}" class="theme-btn  btn-success">{{__('Config Clear')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card status-card">
                                <div class="card-body py-0">
                                    <div class="status-card-inner">
                                        <div class="content">
                                            <p>{{__('Clear Application View / Route')}}</p>
                                            <small>{{__('By clicking this button you can clear the both view and route cache file')}}</small>
                                            <a href="{{route('adminRunCommand',COMMAND_TYPE_VIEW)}}" class="theme-btn  btn-success">{{__('View Clear')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-xl-4 col-md-6 col-12 mb-xl-0 mb-4">
                            <div class="card status-card">
                                <div class="card-body py-0">
                                    <div class="status-card-inner">
                                        <div class="content">
                                            <p>{{__('Run Migration')}}</p>
                                            <small>{{__('Migrate all db file')}}</small>
                                            <a href="{{route('adminRunCommand',COMMAND_TYPE_MIGRATE)}}" class="theme-btn  btn-success">{{__('Migrate')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card status-card">
                                <div class="card-body py-0">
                                    <div class="status-card-inner">
                                        <div class="content">
                                            <p>{{__('Adjust Coin Wallet')}}</p>
                                            <small>{{__('To adjust the coin at all wallet ,you must run this command. please click this button')}}</small>
                                            <a href="{{route('adminRunCommand',COMMAND_TYPE_WALLET)}}" class="theme-btn  btn-warning">{{__('Adjust Wallet')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /User Management -->

@endsection

@section('script')
    <script>
    </script>
@endsection
