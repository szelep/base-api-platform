<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Role object representing sf user role.
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
            ]
        ],
        itemOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
            ],
        ],
        attributes: [
            'security' => 'is_granted("ROLE_ADMIN")',
        ]
    ),
    ORM\Entity,
    ORM\Table(
        name: 'roles',
        schema: 'users',
    )
]
class Role
{
    use IdentifierTrait;

    public const READ_GROUP = 'Role:read';

    /**
     * Role name in Symfony notation like `ROLE_XXX`.
     */
    #[
        ORM\Column(type: 'text'),
        Groups([
            self::READ_GROUP
        ])
    ]
    private string $name;

    /**
     * Returns string.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
