<?php


namespace TheCodingMachine\Discovery;

use TheCodingMachine\Discovery\Utils\JsonException;

class AssetOperationTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFromString()
    {
        $assetOperation = AssetOperation::buildFromString('foo', 'bar/baz', 'vendor/bar/baz');
        $this->assertSame(AssetOperation::ADD, $assetOperation->getOperation());
        $this->assertSame('foo', $assetOperation->getAsset()->getValue());
        $this->assertEquals(0, $assetOperation->getAsset()->getPriority());
        $this->assertSame([], $assetOperation->getAsset()->getMetadata());
        $this->assertSame('bar/baz', $assetOperation->getAsset()->getPackage());
        $this->assertSame('vendor/bar/baz', $assetOperation->getAsset()->getPackageDir());
    }

    public function testBuildFromArray()
    {
        $assetOperation = AssetOperation::buildFromArray([
            'value' => 'foo',
            'priority' => 99,
            'metadata' => [
                'foo' => 'bar'
            ]
        ], 'bar/baz', 'vendor/bar/baz');
        $this->assertSame(AssetOperation::ADD, $assetOperation->getOperation());
        $this->assertSame('foo', $assetOperation->getAsset()->getValue());
        $this->assertEquals(99, $assetOperation->getAsset()->getPriority());
        $this->assertSame([
            'foo' => 'bar'
        ], $assetOperation->getAsset()->getMetadata());
        $this->assertSame('bar/baz', $assetOperation->getAsset()->getPackage());
        $this->assertSame('vendor/bar/baz', $assetOperation->getAsset()->getPackageDir());
    }

    public function testBuildFromArrayWithMissingValues()
    {
        $assetOperation = AssetOperation::buildFromArray([
            'value' => 'foo',
        ], 'bar/baz', 'vendor/bar/baz');
        $this->assertSame(AssetOperation::ADD, $assetOperation->getOperation());
        $this->assertSame('foo', $assetOperation->getAsset()->getValue());
        $this->assertSame(0.0, $assetOperation->getAsset()->getPriority());
        $this->assertSame([], $assetOperation->getAsset()->getMetadata());
    }

    public function testBuildFromArrayWithBadKey()
    {
        $this->expectException(JsonException::class);
        AssetOperation::buildFromArray([
            'badkey' => 'foo',
            'value' => 'foo'
        ], 'bar/baz', 'vendor/bar/baz');
    }

    public function testBuildFromArrayWithNoValue()
    {
        $this->expectException(JsonException::class);
        AssetOperation::buildFromArray([
        ], 'bar/baz', 'vendor/bar/baz');
    }

    public function testConstructorError()
    {
        $this->expectException(InvalidArgumentException::class);
        new AssetOperation('boom', new Asset("value", "package", "packagedir", 0, []));
    }

    public function testToSimpleArray()
    {
        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 0.0, null);
        $assetOperation = new AssetOperation(AssetOperation::REMOVE, $asset);

        $this->assertSame([
            'value' => 'foo',
            'action' => 'remove'
        ], $assetOperation->toSimpleArray());

        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 0.0, null);
        $assetOperation = new AssetOperation(AssetOperation::ADD, $asset);

        $this->assertSame('foo', $assetOperation->toSimpleArray());
    }
}
