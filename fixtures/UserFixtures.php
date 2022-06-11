<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User fixtures.
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Public constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @var array
     */
    private const BANK = [
        [
            'username' => 'user',
            'password' => 'password',
            'id' => '9034a2a0-fe72-41d7-a47f-521f24cdf791',
            'roles' => [
                'role_user_ref',
            ],
            'ref' => 'user_user_ref',
        ],
        [
            'username' => 'admin',
            'password' => 'password',
            'id' => '9f2a544b-a08b-4c28-8e78-77b1d912feab',
            'roles' => [
                'role_user_ref',
                'role_admin_ref',
            ],
            'ref' => 'user_admin_ref',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager): void
    {
        $metadata = $manager->getClassMetadata(User::class);
        assert($metadata instanceof ClassMetadata);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::BANK as $userData) {
            $user = (new User())
                ->setUsername($userData['username'])
            ;

            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $userData['password'])
            );

            foreach ($userData['roles'] as $roleRef) {
                $user->addRole($this->getReference($roleRef));
            }

            $metadata->setIdentifierValues($user, ['id' => $userData['id']]);
            $manager->persist($user);
            $this->addReference($userData['ref'], $user);
        }

        $manager->flush();

        $metadata->setIdGenerator(new UuidGenerator());
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}
