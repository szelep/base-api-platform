<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\{
    DataPersisterInterface,
    ResumableDataPersisterInterface
};
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * API persister supporting User entity.
 */
class UserPersister implements DataPersisterInterface, ResumableDataPersisterInterface
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * {@inheritDoc}
     *
     * Provides password hashing for user with plainPassword set.
     */
    public function persist($data): void
    {
        assert($data instanceof User);

        if ($data->getPlainPassword() !== null) {
            $hashedPassword = $this
                ->passwordHasher
                ->hashPassword(
                    $data,
                    $data->getPlainPassword()
                );

            $data->setPassword($hashedPassword);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function remove($data): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function resumable(array $context = []): bool
    {
        return true;
    }
}
