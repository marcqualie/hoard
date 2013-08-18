<?php

namespace Hoard\Test\Api;

use Hoard\Test\TestCase;

class AuthTest extends TestCase
{

    public function testKeyAuth()
    {

        $api_endpoint = '/api/stats';

        // Not authed
        $noauth_response = $this->makeRawRequest('GET', $api_endpoint);
        $noauth_array = json_decode($noauth_response->getContent(), true);
        $this->assertSame(401, $noauth_array['error']['code']);
        $this->assertEquals('API Key is required', $noauth_array['error']['message']);

        // Authed
        $auth_response = $this->makeApiRequest('GET', $api_endpoint);
        $auth_array = json_decode($auth_response->getContent(), true);
        $this->assertSame(500, $auth_array['error']['code']);
        $this->assertEquals('No bucket specified', $auth_array['error']['message']);


    }

}
