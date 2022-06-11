<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Double\RequestHelper;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;

/**
 * API tests for User.
 */
class UserTest extends ApiTestCase
{
    use RequestHelper;

    /**
     * @return void
     */
    public function testPublicPostUserConstraintsViolation(): void
    {
        $this->request(
            'api/public/users',
            Request::METHOD_POST,
            [],
            throwException: false,
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'plainPassword: This value should not be null.
repeatedPlainPassword: This value should not be null.
username: This value should not be null.'
        ]);
    }

    /**
     * @return void
     */
    public function testSuccessfullPublicPost(): void
    {
        $this->request(
            'api/public/users',
            Request::METHOD_POST,
            [
                'username' => 'some_new_user',
                'plainPassword' => 'pa$$word',
                'repeatedPlainPassword' => 'pa$$word',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'username' => 'some_new_user',
        ]);
    }

    /**
     * @testdox regular ROLE_USER user can not fetch other users
     *
     * @return void
     */
    public function testUserFetchOtherSecurity(): void
    {
        $this->auth('user', 'password');

        $this->expectException(ClientException::class);

        $this->request(
            'api/users/9f2a544b-a08b-4c28-8e78-77b1d912feab'
        );
    }

    /**
     * @dataProvider usersProvider
     *
     * @param string $userId
     *
     * @return void
     */
    public function testFetchAnyUserByAdmin(string $userId): void
    {
        $this->auth('admin', 'password');

        $this->request(
            'api/users/' . $userId,
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
        ]);
    }

    /**
     * @return array
     */
    public function usersProvider(): array
    {
        return [
            'admin' => ['9f2a544b-a08b-4c28-8e78-77b1d912feab'],
            'user' => ['9034a2a0-fe72-41d7-a47f-521f24cdf791']
        ];
    }

    /**
     * @return void
     */
    public function testRegularUserFetchItself(): void
    {
        $this->auth('user', 'password');

        $this->request(
            'api/users/9034a2a0-fe72-41d7-a47f-521f24cdf791',
        );

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@type' => 'User',
            'id' => '9034a2a0-fe72-41d7-a47f-521f24cdf791',
            'username' => 'user',
        ]);
    }

    /**
     * @testdox login to newly created account should not throw an exception
     *
     * @return void
     */
    public function testAuthToNewAccount(): void
    {

        $this->request(
            'api/public/users',
            Request::METHOD_POST,
            [
                'username' => 'user_to_auth',
                'plainPassword' => 'password',
                'repeatedPlainPassword' => 'password',
            ],
        );

        $this->auth('user_to_auth', 'password');

        $this->addToAssertionCount(1);
    }
}
