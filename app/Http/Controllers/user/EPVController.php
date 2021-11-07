<?php

namespace App\Http\Controllers\user;

use App\Http\Requests\EpvLoginRequest;
use App\Services\EPVPaymentApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EPVController extends Controller
{
    // login with epv
    public function LoginWithEpv(EpvLoginRequest $request)
    {
        try {
            $api = new EPVPaymentApiService();
            $params = ['email' => $request->email, 'password' => $request->password];
            $response = $api->evpLogin($params);
            dd($response);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        dd($request->all());
    }
}
