<?php

namespace Mia\GoToLog\Handler;

use Mia\Auth\Model\MIAProvider;
use Mia\Core\Exception\MiaException;
use Mia\GoToLog\Helper\MiaGotoHelper;
use Mia\GoToLog\Service\MiaGotoService;

/**
 * Description of PayHandler
 *
 * @author matiascamiletti
 */
class GetAllWebinarsHandler extends \Mia\Auth\Request\MiaAuthRequestHandler
{
    /**
     * @var MiaGotoService
     */
    protected $service;

    public function __construct(MiaGotoService $service)
    {
        $this->service = $service;
    }
    /**
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface 
    {
        // Get Current user
        $user = $this->getUser($request);
        // Refresh Token
        $data = MiaGotoHelper::refreshToken($this->service, $user);
        // Set Access Token
        $this->service->setAccessToken($data->access_token);
        // Get params
        $organizerKey = $data->organizer_key;
        $from = $this->getParam($request, 'from', '2021-01-01T00:00:00Z');
        $to = $this->getParam($request, 'to', '2024-12-31T00:00:00Z');
        // Get All Webinars
        $webinars = $this->service->getAllWebinars($organizerKey, $from, $to);

        return new \Mia\Core\Diactoros\MiaJsonResponse($webinars);
    }
}
