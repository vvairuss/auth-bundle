<?php

use PHPUnit\Framework\TestCase;
use Svyaznoy\Bundle\AuthBundle\Authenticator\AccessToken;

class AccessTokenTest extends TestCase
{
    public function testConstructAccessToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AccessToken([]);
    }

    public function testConstructExpressNotNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AccessToken(['expires_in' => 'not number']);
    }

    public function testConstructOptions(): void
    {
        $accessToken = new AccessToken([
            'access_token' => 'tokenTest',
            'resource_owner_id' => 'testResourceOwner',
            'refresh_token' => 'testRefreshToken',
            'expires_in' => 360,
        ]);

        $this->assertSame($accessToken->getToken(), 'tokenTest', 'constructor not save access_token');
        $this->assertSame($accessToken->getResourceOwnerId(), 'testResourceOwner', 'constructor not save resource_owner_id');
        $this->assertSame($accessToken->getRefreshToken(), 'testRefreshToken', 'constructor not save refresh_token');
        $this->assertTrue($accessToken->getExpires() > 360,  'constructor not append time to expires_in');

        $accessToken = new AccessToken([
            'access_token' => 'tokenTest',
            'expires' => 360,
        ]);

        $this->assertNull($accessToken->getResourceOwnerId(), 'constructor not save resource_owner_id to null');
        $this->assertNull($accessToken->getRefreshToken(), 'constructor not save refresh_token not null');
        $this->assertTrue($accessToken->getExpires() > 360,  'constructor not append time to expires_in');

    }

    public function testHasExpired()
    {
        $accessToken = new AccessToken([
            'access_token' => 'tokenTest',
            'expires' => time() + (-1000),
        ]);
        $this->assertTrue($accessToken->hasExpired());

        $accessToken = new AccessToken([
            'access_token' => 'tokenTest',
            'expires' => time() + 10000,
        ]);
        $this->assertTrue($accessToken->hasExpired());

        new AccessToken(['access_token' => 'tokenTest']);
        $this->expectException(RuntimeException::class);
    }
}
