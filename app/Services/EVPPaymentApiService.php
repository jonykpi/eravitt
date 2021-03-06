<?php
namespace App\Services;
/*
	EVP Payment Api Service
*/

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;


class EVPPaymentApiService {
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
            'Client-Service' => $this->client_service,
            'Auth-Key' => $this->auth_key,
            'Content-Type'=> 'application/json',
        ];
    }

    public function callApi($method,$endPoint,$params=[])
    {
        try {
            $client = new GuzzleClient();
            $url = $this->base_url.$endPoint;

            $response = $client->request('POST', $url, ['body' => $params,  'headers' => $this->header]);

            $result = (string)$response->getBody();
            $result = json_decode($result);
            return $result;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $exception = (string)$e->getResponse()->getBody();
                $exception = json_decode($exception);
                return $exception;
                return new JsonResponse($exception, $e->getCode());
            } else {
                return new JsonResponse($e->getMessage(), 503);
            }
        }
    }

    public function evpLogin($postData)
    {
        $params = json_encode($postData);
        $response = $this->callApi('POST','login',$params);

        return $response;
    }

    public function evpCheckout($postData)
    {
        $params = json_encode($postData);
        $response = $this->callApi('POST','wallet_checkout',$params);

        return $response;
    }


};



