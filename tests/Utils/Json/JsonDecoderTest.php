<?php

namespace Pingframework\Ping\Tests\Utils\Json;

use JsonException;
use PHPUnit\Framework\TestCase;
use Pingframework\Ping\Utils\Json\JsonDecoder;

class JsonDecoderTest extends TestCase
{
    public function testUnmarshal()
    {
        $json = '{"foo": "bar"}';
        $expected = ['foo' => 'bar'];
        $decoder = new JsonDecoder();
        $this->assertEquals($expected, $decoder->unmarshal($json));
    }

    public function testUnmarshalDefault()
    {
        $json = null;
        $expected = ['foo' => 'bar'];
        $decoder = new JsonDecoder();
        $this->assertEquals($expected, $decoder->unmarshal($json, $expected));
    }

    public function testUnmarshalException()
    {
        $json = null;
        $decoder = new JsonDecoder();
        $this->expectException(JsonException::class);
        $decoder->unmarshal($json);
    }

    public function testUnmarshalNonArray()
    {
        $json = "true";
        $decoder = new JsonDecoder();
        $this->expectException(JsonException::class);
        $decoder->unmarshal($json);
    }

    public function testUnmarshalNonArrayDefault()
    {
        $json = "true";
        $decoder = new JsonDecoder();
        $this->expectException(JsonException::class);
        $decoder->unmarshal($json, ['foo' => 'bar']);
    }
}
