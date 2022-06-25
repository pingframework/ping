<?php

namespace Pingframework\Ping\Tests\DependencyContainer\Definition;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\DependencyContainer\Definition\AttributeClassDefinitionMap;
use ReflectionClass;

class AttributeClassDefinitionMapTest extends TestCase
{

    public function testGet()
    {
        $m = new AttributeClassDefinitionMap();
        $rc = new ReflectionClass(TestClass::class);
        $m->put($rc);

        $result = $m->get(TestAttribute1::class, true);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($rc, $result[0]);
    }
}
