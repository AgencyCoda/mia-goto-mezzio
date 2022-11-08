<?php

namespace Mia\GoToLog\Service;

use GuzzleHttp\Psr7\Request;

class MiaGotoService
{
    /**
     * URL de la API
     */
    const BASE_URL = 'https://api.getgo.com/';
    const OATUH_BASE_URL = 'https://authentication.logmeininc.com/oauth/';
    const BASE_URL_WEBINARS = 'https://api.getgo.com/G2W/rest/v2/';
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
     *
     * @var string
     */
    protected $redirectUrl = '';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    public function __construct($client_id, $client_secret, $redirect_url = '')
    {
        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->redirectUrl = $redirect_url;
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function getAuthorizeUrl()
    {
        return self::OATUH_BASE_URL . 'authorize?client_id=' . $this->clientId . '&response_type=code&redirect_uri=' . $this->redirectUrl;
        //return 'https://api.getgo.com/oauth/v2/authorize?client_id=' . $this->clientId . '&response_type=code&redirect_uri=' . $redirectUrl;
    }

    public function getAllWebinars($organizerKey, $fromTime = '2020-03-13T10:00:00Z', $toTime = '2020-03-13T10:00:00Z')
    {
        return $this->generateRequest('GET', self::BASE_URL_WEBINARS . 'organizers/'.$organizerKey.'/webinars?fromTime=' . $fromTime . '&toTime=' . $toTime . '');
    }

    public function getWebinar($organizerKey, $webinarKey)
    {
        return $this->generateRequest('GET', self::BASE_URL_WEBINARS . 'organizers/'.$organizerKey.'/webinars/' . $webinarKey);
    }

    public function getAllAttendes($organizerKey, $webinarKey)
    {
        return $this->generateRequest('GET', self::BASE_URL_WEBINARS . 'organizers/'.$organizerKey.'/webinars/' . $webinarKey . '/attendees');
    }

    public function getAllRegistrants($organizerKey, $webinarKey)
    {
        return $this->generateRequest('GET', self::BASE_URL_WEBINARS . 'organizers/'.$organizerKey.'/webinars/' . $webinarKey . '/registrants');
    }

    public function createRegistrant($organizerKey, $webinarKey, $firstname, $lastname, $email, $country, $organization, $jobTitle)
    {
        return $this->generateRequest('POST', self::BASE_URL_WEBINARS . 'organizers/'.$organizerKey.'/webinars/' . $webinarKey . '/registrants', [
            'firstName' => $firstname,
            'lastName' => $lastname,
            'email' => $email,
            'country' => $country,
            'organization' => $organization,
            'jobTitle' => $jobTitle
        ]);
    }

    public function createWebhookSecretKey()
    {
        return $this->generateRequest('POST', self::BASE_URL_WEBINARS . 'webhooks/secretkey');
    }

    public function createWebhook($callbackUrl, $eventName, $eventVersion, $product)
    {
        return $this->generateRequest('POST', self::BASE_URL_WEBINARS . 'webhooks', [
            'callbackUrl' => $callbackUrl,
            'eventName' => $eventName,
            'eventVersion' => $eventVersion,
            'product' => $product
        ]);
    }

    public function activateWebhook($webhookKey, $callbackUrl)
    {
        return $this->generateRequest('PUT', self::BASE_URL_WEBINARS . 'webhooks', [
            'callbackUrl' => $callbackUrl,
            'webhookKey' => $webhookKey,
            'state' => 'ACTIVE'
        ]);
    }

    public function createNewUserSubscription($webhookKey, $callbackUrl)
    {
        return $this->generateRequest('PUT', self::BASE_URL_WEBINARS . 'userSubscriptions', [
            'callbackUrl' => $callbackUrl,
            'webhookKey' => $webhookKey,
            'userSubscriptionState' => 'ACTIVE'
        ]);
    }

    public function getMe()
    {
        return $this->generateRequest('GET', 'https://api.getgo.com/admin/rest/v1/me');
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

        if($response->getStatusCode() == 200||$response->getStatusCode() == 201){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function generateAccessToken($code)
    {
        $response = $this->guzzle->request('POST', self::OATUH_BASE_URL . 'token', [
            'headers' => $this->getHeadersBasic(),
            'form_params' => [
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
                'code' => $code
            ]
        ]);

        if($response->getStatusCode() == 200||$response->getStatusCode() == 201){
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
        if($response->getStatusCode() == 200||$response->getStatusCode() == 201){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
}