<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\Entity\User;
use App\EventListener\JWTCreatedListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Tests for JwtCreatedListener class.
 *
 * @SuppressWarnings(PHPMD.CommentDescription)
 */
class JWTCreatedListenerTest extends TestCase
{
    /**
     * @testdox profile name should be appended to JWT payload
     *
     * @return void
     */
    public function testAdditionalDataAppend(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock
            ->method('getRoles')
            ->willReturn([
                'ROLE_1',
            ])
        ;
        $userMock
            ->method('getId')
            ->willReturn('e66f612d-7211-417b-a889-9118604553a9')
        ;
        $eventData = [
            'should_keep_this' => true,
        ];
        $roleHierarchyMock = $this->createMock(RoleHierarchyInterface::class);
        $roleHierarchyMock
            ->method('getReachableRoleNames')
            ->willReturn(['SOME_ROLE_1', 'SOME_ROLE_2'])
        ;
        $jwtCreatedListener = new JwtCreatedListener($roleHierarchyMock);
        $event = new JwtCreatedEvent($eventData, $userMock);

        $jwtCreatedListener->onJwtCreated($event);

        $this->assertArrayHasKey('should_keep_this', $event->getData());
        $this->assertEquals(['SOME_ROLE_1', 'SOME_ROLE_2'], $event->getData()['roles']);
        $this->assertEquals('e66f612d-7211-417b-a889-9118604553a9', $event->getData()['id']);
    }
}
