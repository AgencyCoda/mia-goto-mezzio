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
class IsConnectedHandler extends \Mia\Auth\Request\MiaAuthRequestHandler
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
        // Verify exist connection
        $provider = MIAProvider::where('user_id', $user->id)->where('provider_type', MIAProvider::PROVIDER_GOTO)->first();
        if($provider === null){
            return new \Mia\Core\Diactoros\MiaJsonResponse(false);
        }

        return new \Mia\Core\Diactoros\MiaJsonResponse(true);
    }
}
