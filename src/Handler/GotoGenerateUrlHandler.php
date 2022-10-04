<?php

namespace Mia\GoToLog\Handler;

use Mia\GoToLog\Service\MiaGotoService;

/**
 * Description of PayHandler
 *
 * @author matiascamiletti
 */
class GotoGenerateUrlHandler extends \Mia\Auth\Request\MiaAuthRequestHandler
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
        return new \Mia\Core\Diactoros\MiaJsonResponse([
            'url' => $this->service->getAuthorizeUrl()
        ]);
    }
}
