<?php

namespace App\Auth;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\User as PassportUser;
use Laravel\Passport\Bridge\UserRepository as BaseUserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class CustomUserRepository extends BaseUserRepository
{
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $client
    ) {
        $provider = request()->input('provider', config('auth.guards.api.provider'));
        $providerModel = config("auth.providers.{$provider}.model");
        if (!class_exists($providerModel)) {
            throw new \RuntimeException("Unable to determine authentication model from configuration.");
        }
        $userProvider = Auth::createUserProvider($provider);
        $user = $userProvider->retrieveByCredentials([
            'email' => $username,
        ]);
        if (! $user || ! $userProvider->validateCredentials($user, ['password' => $password])) {
            return null;
        }
        return new PassportUser($user->getAuthIdentifier());
    }
}
