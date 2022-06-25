<?php

namespace Pingframework\Ping\Tests\DependencyContainer\Definition;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\DependencyContainer\Definition\VariadicDefinitionMap;

class VariadicDefinitionMapTest extends TestCase
{
    public function testGet()
    {
        $vm = new VariadicDefinitionMap();
        $vm->put('s1', 'd3', -1);
        $vm->put('s1', 'd1', 0);
        $vm->put('s1', 'd2', 1);

        // duplicate
        $vm->put('s1', 'd2', 1);

        $result = $vm->get('s1');

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals(['d2', 'd1', 'd3'], $result);
    }

    public function testRemove()
    {
        $vm = new VariadicDefinitionMap();
        $vm->put('s1', 'd1');
        $vm->put('s1', 'd2');
        $vm->put('s1', 'd3');

        $vm->remove('s1', 'd2');
        $result = $vm->get('s1');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals(['d1', 'd3'], $result);
    }
}
