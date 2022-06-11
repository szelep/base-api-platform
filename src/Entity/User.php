<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection
};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\{
    PasswordAuthenticatedUserInterface,
    UserInterface
};
use Symfony\Component\Serializer\Annotation\{
    Groups,
    Ignore
};
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Object represeting User.
 */
#[
    ORM\Entity,
    ORM\Table(
        name: 'users',
        schema: 'users'
    ),
    ApiResource(
        collectionOperations: [
            'get' => [
                'security' => 'is_granted("ROLE_ADMIN")',
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
            ],
            'register' => [
                'method' => Request::METHOD_POST,
                'path' => '/public/users',
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
                'denormalization_context' => [
                    'groups' => [
                        self::WRITE_REGISTER_GROUP,
                    ]
                ],
                'validation_groups' => ['create'],
            ]
        ],
        itemOperations: [
            'get' => [
                'security' => 'is_granted("ROLE_ADMIN") or object === user',
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
            ],
        ],
    ),
    UniqueEntity(fields: ['username'], groups: ['User', 'create'])
]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentifierTrait;

    /**
     * Serialization groups.
     *
     * @var string
     */
    public const READ_GROUP = 'User:read';
    public const WRITE_REGISTER_GROUP = 'User:write:register';

    /**
     * Roles.
     */
    #[
        ORM\ManyToMany(targetEntity: Role::class),
        ORM\JoinTable(
            name: 'users_roles',
            schema: 'users',
        ),
    ]
    private Collection $roles;

    /**
     * Password.
     */
    #[
        ORM\Column(type: 'text', nullable: true),
        Ignore,
    ]
    private string|null $password;

    /**
     * Plain password used in account create.
     */
    #[
        Groups([
            self::WRITE_REGISTER_GROUP,
        ]),
        Assert\NotNull(groups: ['create']),
    ]
    private ?string $plainPassword;

    /**
     * Must be same as `plainPassword`.
     */
    #[
        Groups([self::WRITE_REGISTER_GROUP]),
        Assert\Expression(
            expression: 'value === this.getPlainPassword()',
            message: 'Password does not match confirmation.',
            groups: ['create'],
        ),
        Assert\NotNull(groups: ['create']),
    ]
    private ?string $repeatedPlainPassword;

    /**
     * Username.
     */
    #[
        ORM\Column(type: 'text'),
        Groups([
            self::READ_GROUP,
            self::WRITE_REGISTER_GROUP,
        ]),
        Assert\NotNull(groups: ['create']),
    ]
    private string $username;

    /**
     * Public constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->plainPassword = null;
        $this->repeatedPlainPassword = null;
        $this->id = Uuid::v4()->toRfc4122();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(): self
    {
        return $this;
    }

    /**
     * Gets username.
     *
     * @param string $username
     *
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets username.
     */
    public function getUsername(): string
    {
        return $this->username;
    }


    /**
     * Gets roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return array_unique([
            'ROLE_USER',
            ...$this
                ->roles
                ->map(fn(Role $role) => $role->getName())
                ->getValues(),
        ]);
    }

    /**
     * Adds role.
     *
     * @param Role $role
     *
     * @return self
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Removes role.
     *
     * @param Role $role
     *
     * @return self
     */
    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * Check if user has role.
     *
     * @param string $roleName
     *
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoles(), true);
    }

    /**
     * Sets password.
     *
     * @param string|null $password
     *
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets plainPassword.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Sets plainPassword.
     *
     * @param string|null $plainPassword
     *
     * @return self
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Gets repeatedPlainPassword.
     *
     * @return string|null
     */
    public function getRepeatedPlainPassword(): ?string
    {
        return $this->repeatedPlainPassword;
    }

    /**
     * Sets repeatedPlainPassword.
     *
     * @param string|null $repeatedPlainPassword
     *
     * @return self
     */
    public function setRepeatedPlainPassword(?string $repeatedPlainPassword): self
    {
        $this->repeatedPlainPassword = $repeatedPlainPassword;

        return $this;
    }
}
