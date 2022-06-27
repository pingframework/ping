<?php

namespace Pingframework\Ping\Tests\DependencyContainer\Builder;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\DependencyContainer\Builder\ContainerBuilder;
use Pingframework\Ping\DependencyContainer\DependencyContainerInterface;
use ReflectionObject;

class ContainerBuilderTest extends TestCase
{

    public function testBuild()
    {
        $c = ContainerBuilder::build([], processAutowiredServices: true);
        $this->assertInstanceOf(DependencyContainerInterface::class, $c);

        $ro = new ReflectionObject($c);
        $rp = $ro->getProperty('resolved');
        $resolved = $rp->getValue($c);
        $this->assertArrayHasKey(TestService4::class, $resolved);

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
        $this->assertInstanceOf(TestService1::class, $ts5->testService1);

        $tmr = $c->get(TestMethodRegistry::class);
        $this->assertIsArray($tmr->getMap());
        $this->assertArrayHasKey('k1', $tmr->getMap());
        $c->call($c->get($tmr->getMap()['k1'][0]), $tmr->getMap()['k1'][1]);

        $ts8 = $c->get(TestService8::class);
        $this->assertInstanceOf(TestService1::class, $ts8->testService1);
    }
}
