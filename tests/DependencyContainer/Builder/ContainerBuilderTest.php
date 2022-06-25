<?php

namespace Pingframework\Ping\Tests\DependencyContainer\Builder;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\DependencyContainer\Builder\ContainerBuilder;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;

class ContainerBuilderTest extends TestCase
{

    public function testBuild()
    {
        $c = ContainerBuilder::build([]);
        $this->assertInstanceOf(DependencyContainerInterface::class, $c);

        $ts2 = $c->get(TestService2::class);
        $this->assertInstanceOf(TestService2::class, $ts2);

        $ts4 = $c->get(TestService4::class);
        $this->assertInstanceOf(TestService4::class, $ts4);
        $this->assertEquals(1, $ts4->x);

        $ts6 = $c->get(TestService6::class);
        $this->assertInstanceOf(TestService6::class, $ts6);
        $this->assertIsArray($ts6->s);
        $this->assertCount(1, $ts6->s);
        $this->assertInstanceOf(TestService5::class, $ts6->s[0]);

        $ts5 = $c->get(TestService5::class);
        $this->assertInstanceOf(TestService5::class, $ts5);
        $this->assertInstanceOf(TestService7::class, $ts5->testService7);
    }
}
