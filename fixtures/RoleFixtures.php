<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * Role fixtures.
 */
class RoleFixtures extends Fixture
{
    /**
     * @var array
     */
    private const BANK = [
        [
            'id' => '5304bd7c-d6d3-48cc-92ca-4c9c399fb84a',
            'name' => 'ROLE_USER',
            'ref' => 'role_user_ref',
        ],
        [
            'id' => '392273f3-41d8-4ff6-8012-b8a1ef1f5c47',
            'name' => 'ROLE_ADMIN',
            'ref' => 'role_admin_ref',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetadata(Role::class);
        assert($metadata instanceof ClassMetadata);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::BANK as $roleData) {
            $role = (new Role())
                ->setName($roleData['name'])
            ;
            $metadata->setIdentifierValues($role, ['id' => $roleData['id']]);
            $manager->persist($role);
            $this->addReference($roleData['ref'], $role);
        }

        $manager->flush();

        $metadata->setIdGenerator(new UuidGenerator());
    }
}
