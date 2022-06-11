<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Provides mapped $id property with getter.
 *
 * @SuppressWarnings(PHPMD.TraitPublicMethod)
 */
trait IdentifierTrait
{
    /**
     * Mapped identifier property.
     */
    #[
        ORM\Id,
        ORM\Column(type: 'uuid', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: UuidGenerator::class),
        Groups(['Identifier:read'])
    ]
    protected string $id;

    /**
     * Gets id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
