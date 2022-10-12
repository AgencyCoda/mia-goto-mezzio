<?php

namespace Mia\GoToLog\Handler;

use Mia\Auth\Model\MIAProvider;
use Mia\Core\Exception\MiaException;
use Mia\GoToLog\Service\MiaGotoService;

/**
 * Description of PayHandler
 *
 * @author matiascamiletti
 */
class GenerateAccessTokenHandler extends \Mia\Auth\Request\MiaAuthRequestHandler
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
        $user = $this->getUser($request);
        $code = $this->getParam($request, 'code', '');

        $data = $this->service->generateAccessToken($code);
        if($data === null){
            throw new MiaException('Problem with code');
        }

        $provider = MIAProvider::where('user_id', $user->id)->where('provider_type', MIAProvider::PROVIDER_GOTO)->first();
        if($provider === null){
            $provider = new MIAProvider();
            $provider->user_id = $user->id;
            $provider->provider_type = MIAProvider::PROVIDER_GOTO;
        }

        $provider->token = json_encode($data);
        $provider->save();

        return new \Mia\Core\Diactoros\MiaJsonResponse(true);
    }
}
