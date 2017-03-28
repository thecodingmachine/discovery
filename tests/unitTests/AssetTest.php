<?php

namespace TheCodingMachine\Discovery;


class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testToSimpleArray()
    {
        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 0.0, null);

        $this->assertSame('foo', $asset->toSimpleArray());

        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 1.0, ['bar' => 'baz']);

        $this->assertSame([
            'value' => 'foo',
            'priority' => 1.0,
            'metadata' => ['bar' => 'baz']
        ], $asset->toSimpleArray());
    }
}
