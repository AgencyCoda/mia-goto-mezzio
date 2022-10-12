<?php

namespace Mia\GoToLog\Helper;

use Mia\Auth\Model\MIAProvider;
use Mia\Core\Exception\MiaException;
use Mia\GoToLog\Service\MiaGotoService;

class MiaGotoHelper
{
    public static function refreshToken(MiaGotoService $service, $user)
    {
        $provider = MIAProvider::where('user_id', $user->id)->where('provider_type', MIAProvider::PROVIDER_GOTO)->first();
        if($provider === null){
            throw new MiaException('Not exist');
        }
        // Convert Json
        $oldData = json_decode($provider->token);
        // Refresh Token
        $data = $service->refreshAccessToken($oldData->refresh_token);
        // Save data
        if($data === null){
            throw new MiaException('Problem with refresh token');
        }
        $provider->token = json_encode($data);
        $provider->save();

        return $data;
    }
}