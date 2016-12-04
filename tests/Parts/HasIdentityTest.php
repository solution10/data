<?php

namespace Solution10\Atlas\Tests\Parts;

use Solution10\Atlas\Parts\HasIdentity;
use Solution10\Atlas\PHPUnit\TestCase;
use Solution10\Atlas\ReflectionPopulate;

class HasIdentityTest extends TestCase
{
    use ReflectionPopulate;

    public function testGetId()
    {
        $t = $this->getMockForTrait(HasIdentity::class);
        $this->populateWithReflection($t, ['id' => 27]);

        $this->assertEquals(27, $t->getId());
    }

    public function testGetIdentityProperty()
    {
        $t = $this->getMockForTrait(HasIdentity::class);
        $this->assertEquals('id', $t->getIdentityProperty());

        $t = new class
        {
            use HasIdentity;

            public function getIdentityProperty(): string
            {
                return 'my_id';
            }
        };
        $this->assertEquals('my_id', $t->getIdentityProperty());
    }
}