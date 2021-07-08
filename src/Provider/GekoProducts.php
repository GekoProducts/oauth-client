<?php

namespace GekoProducts\OAuth\Provider;

use GekoProducts\OAuth\Exceptions\GekoProductsClientException;
use GekoProducts\OAuth\Exceptions\GekoProductsServerException;
use GekoProducts\OAuth\Exceptions\MethodNotConfiguredException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class GekoProducts extends AbstractProvider {

    use BearerAuthorizationTrait;

    public $domain = "https://auth.gekoproducts.co.uk";

    public function getBaseAuthorizationUrl()
    {
        return $this->domain . "/oidc/access_token";
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain . "/oidc/access_token";
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        throw new MethodNotConfiguredException;
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new GekoProductsClientException($response, $data);
        } else if (isset($data["error"])) {
            throw new GekoProductsServerException($response, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        throw new MethodNotConfiguredException;
    }
}
