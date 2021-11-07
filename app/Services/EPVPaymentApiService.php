<?php
namespace App\Services;
/*
	EPV Payment Api Service
*/

use GuzzleHttp\Client as GuzzleClient;


class EPVPaymentApiService {
	private $base_url;
	private $auth_key;
	private $header;
	private $client_service;

    public function __construct()
    {
        $this->auth_key = 'J@NcRfUjXn2r5u8x/A?D(G-KaPdSgVkY';
        $this->client_service = 'eravitt-client';
        $this->base_url = 'https://www.eravitt.com/api/';

        $this->header =  [
            'Client-Service' => $this->auth_key,
            'Auth-Key' => $this->client_service,
            'Accept'     => 'application/json',
        ];
    }

    public function callApi($method,$endPoint,$params=[])
    {
        $client = new GuzzleClient([
            'headers' => $this->header
        ]);
        $url = $this->base_url.$endPoint;
        $response = $client->request('POST', $url, ['form_params' => $params]);

        return $response;

    }

    public function evpLogin($params)
    {
        $response = $this->callApi('POST','login',$params);

        return $response;
    }


};



