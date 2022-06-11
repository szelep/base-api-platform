<?php

declare(strict_types=1);

namespace App\Tests\DataPersister;

use App\DataPersister\UserPersister;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests for UserPersister class.
 */
class UserPersisterTest extends TestCase
{
    /**
     * @return void
     */
    public function testSupports(): void
    {
        $persister = new UserPersister($this->createMock(UserPasswordHasherInterface::class));

        $this->assertFalse($persister->supports(new stdClass()));
        $this->assertFalse($persister->supports($persister));
        $this->assertTrue($persister->supports($this->createMock(User::class)));
    }

    /**
     * @return void
     */
    public function testResumable(): void
    {
        $persister = new UserPersister($this->createMock(UserPasswordHasherInterface::class));

        $this->assertTrue($persister->resumable());
    }

    /**
     * @testdox user plain password is null, hasher should not be triggered
     *
     * @return void
     */
    public function testPasswordNotHashed(): void
    {
        $passwordHasherMock = $this->createMock(UserPasswordHasher::class);
        $passwordHasherMock->expects($this->never())->method('hashPassword');
        $persister = new UserPersister($passwordHasherMock);

        $persister->persist($this->createMock(User::class));
    }

    /**
     * @testox if plain password is not null, password should be hashed and set to user
     *
     * @return void
     */
    public function testPasswordHashedAndSet(): void
    {
        $passwordHasherMock = $this->createMock(UserPasswordHasher::class);
        $passwordHasherMock->method('hashPassword')->willReturn('encodedPass');
        $userMock = $this->createPartialMock(User::class, ['getPlainPassword']);
        $userMock->method('getPlainPassword')->willReturn('plainPass');

        $persister = new UserPersister($passwordHasherMock);
        $persister->persist($userMock);

        $this->assertSame('encodedPass', $userMock->getPassword());
    }
}
