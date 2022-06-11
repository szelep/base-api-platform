<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Double\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authentication by API tests.
 */
class AuthenticationTest extends ApiTestCase
{
    use RequestHelper;

    /**
     * @return void
     */
    public function testValidCredentials(): void
    {
        $this->request(
            '/authentication_token',
            Request::METHOD_POST,
            [
                'username' => 'admin',
                'password' => 'password',
            ]
        );

        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     */
    public function testInvalidCredentials(): void
    {
        $this->request(
            '/authentication_token',
            Request::METHOD_POST,
            [
                'username' => 'random',
                'password' => 'unknown',
            ],
            throwException: false,
        );

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'message' => 'Invalid credentials.',
        ]);
    }
}
