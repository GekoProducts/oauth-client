<?php

namespace GekoProducts\Tests\Provider;

use GekoProducts\OAuth\Exceptions\GekoProductsClientException;
use GekoProducts\OAuth\Exceptions\MethodNotConfiguredException;
use GekoProducts\OAuth\Provider\GekoProducts;
use Mockery;
use PHPUnit\Framework\TestCase;

class GekoProductsTest extends TestCase {

    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new GekoProducts([
            "clientId" => "1",
            "clientSecret" => "2j2smV28aGtANTVsISfbI4b5cN33b77r8yKr"
        ]);
    }

    public function testGetBaseAuthorizationUrl()
    {
        $url = $this->provider->getBaseAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/oidc/access_token', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('/oidc/access_token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')
            ->andReturn('{"access_token":"mock_access_token", "scope":"test", "token_type":"bearer"}');
        $response->shouldReceive('getHeader')
            ->andReturn(['content-type' => 'application/json']);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testExceptionIsThrownWhenGettingResourceOwner()
    {
        $this->expectException(MethodNotConfiguredException::class);

        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')
            ->andReturn('{"access_token":"mock_access_token", "scope":"test", "token_type":"bearer"}');
        $response->shouldReceive('getHeader')
            ->andReturn(['content-type' => 'application/json']);
        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->provider->getResourceOwnerDetailsUrl($token);
    }

    public function testExceptionIsThrownOnError()
    {
        $this->expectException(GekoProductsClientException::class);

        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')
            ->andReturn('{"error":"invalid_grant", "error_description":"something bad happened", "hint":""}');
        $response->shouldReceive('getHeader')
            ->andReturn(['content-type' => 'application/json']);
        $response->shouldReceive('getStatusCode')
            ->andReturn(400);

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->provider->getResourceOwnerDetailsUrl($token);
    }
}
