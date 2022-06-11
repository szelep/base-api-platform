<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\IdentifierTrait;
use PHPUnit\Framework\TestCase;

/**
 * Tests for IdentifierTrait class.
 */
class IdentifierTraitTest extends TestCase
{
    /**
     * @testdox test trait getId() method
     *
     * @return void
     */
    public function testGetter(): void
    {
        $class = new class {
            use IdentifierTrait;

            /**
             * Constructor.
             */
            public function __construct()
            {
                $this->id = 'identifier';
            }
        };

        $this->assertSame('identifier', $class->getId());
    }
}
