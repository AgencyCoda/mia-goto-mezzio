<?php

namespace Mia\GoToLog\Service;

use GuzzleHttp\Psr7\Request;

class MiaGotoService
{
    /**
     * URL de la API
     */
    const BASE_URL = 'https://api.getgo.com/';
    /**
     * Documentation: https://developer.goto.com/
     * @var string
     */
    protected $clientId = '';
    /**
     * 
     * @var string
     */
    protected $clientSecret = '';
    /**
     *
     * @var string
     */
    protected $accessToken = '';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    public function __construct($client_id, $client_secret)
    {
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function getAuthorizeUrl($redirectUrl)
    {
        return 'https://api.getgo.com/oauth/v2/authorize?client_id=' . $this->clientId . '&response_type=code&redirect_uri=' . $redirectUrl;
    }

    public function getAllWebinars($organizerKey, $fromTime = '2020-03-13T10:00:00Z', $toTime = '2020-03-13T10:00:00Z')
    {
        return $this->generateRequest('POST', 'https://api.getgo.com/G2W/rest/v2/organizers/'.$organizerKey.'/webinars?fromTime=' . $fromTime . '&toTime=' . $toTime . '');
    }

    public function refreshAccessToken($refreshToken)
    {
        $response = $this->guzzle->request('POST', 'https://api.getgo.com/oauth/v2/token', [
            'headers' => $this->getHeadersBasic(),
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            ]
        ]);

        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function generateAccessToken($code, $redirectUrl)
    {
        $response = $this->guzzle->request('POST', 'https://api.getgo.com/oauth/v2/token', [
            'headers' => $this->getHeadersBasic(),
            'form_params' => [
                'redirect_uri' => $redirectUrl,
                'grant_type' => 'authorization_code',
                'code' => $code
            ]
        ]);

        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    protected function getHeadersBasic()
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
        ];
    }
    
    protected function generateRequest($method, $url, $params = null)
    {
        $body = null;
        if($params != null){
            $body = json_encode($params);
        }

        $request = new Request(
            $method, 
            $url, 
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->accessToken
            ], $body);

        $response = $this->guzzle->send($request);
        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
}