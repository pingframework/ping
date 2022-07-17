<?php

namespace Pingframework\Ping\Tests\Utils\Arrays\Composition;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\Utils\Arrays\Arrays;

class GetterTraitTest extends TestCase
{
    public function testIsExists()
    {
        $array = [
            'key' => 'value',
            'key3' => null,
        ];

        $this->assertTrue(Arrays::isExists($array, 'key'));
        $this->assertFalse(Arrays::isExists($array, 'key2'));
        $this->assertTrue(Arrays::isExists($array, 'key3'));
    }
}
