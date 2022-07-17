<?php

namespace Pingframework\Ping\Tests\Utils\ObjectMapper;

use PHPUnit\Framework\TestCase;
use Pingframework\Ping\Utils\ObjectMapper\DefaultObjectMapper;

class ObjectMapperTest extends TestCase
{

    public function testMapListFromArray()
    {
        $om = new DefaultObjectMapper();
        $list = $om->mapListFromArray([
            ['id' => null],
        ], TestObjectMock::class);

        $this->assertCount(1, $list);
        $this->assertNull($list[0]->id);
    }
}
