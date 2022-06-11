<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Class JWTCreatedListener
 */
class JWTCreatedListener
{
    /**
     * Public constructor.
     *
     * @param RoleHierarchyInterface $roleHierarchy
     */
    public function __construct(private RoleHierarchyInterface $roleHierarchy)
    {
    }

    /**
     * Append additional info to JWT Token.
     *
     * @param JwtCreatedEvent $event
     *
     * @return void
     */
    public function onJwtCreated(JwtCreatedEvent $event): void
    {
        $user = $event->getUser();
        assert($user instanceof User);
        $currentData = $event->getData();

        $event->setData([
            ...$currentData,
            'id' => $user->getId(),
            'roles' => $this->roleHierarchy->getReachableRoleNames($user->getRoles()),
        ]);
    }
}
