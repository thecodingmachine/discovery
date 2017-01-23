<?php


namespace TheCodingMachine\Discovery;


use TheCodingMachine\Discovery\Utils\IOException;
use TheCodingMachine\Discovery\Utils\JsonException;

class DiscoveryFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $loader = new DiscoveryFileLoader();

        $assetTypes = $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/package_a/discovery.json'), 'fixture/package_a', 'vendor/fixture/package_a');

        $this->assertCount(1, $assetTypes);
        $assets = $assetTypes['test-asset'];

        $this->assertCount(2, $assets);
        $this->assertInstanceOf(AssetOperation::class, $assets[0]);
        $this->assertSame('a1', $assets[0]->getAsset()->getValue());
        $this->assertSame('a2', $assets[1]->getAsset()->getValue());
    }

    public function testLoadString()
    {
        $loader = new DiscoveryFileLoader();

        $assetTypes = $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/package_b/discovery.json'), 'fixture/package_a', 'vendor/fixture/package_a');

        $this->assertCount(1, $assetTypes);
        $assets = $assetTypes['test-asset'];

        $this->assertCount(1, $assets);
        $this->assertInstanceOf(AssetOperation::class, $assets[0]);
        $this->assertSame('b', $assets[0]->getAsset()->getValue());
    }

    public function testBadJson()
    {
        $loader = new DiscoveryFileLoader();

        $this->expectException(JsonException::class);
        $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/error_cases/bad.json'), 'fixture/package_a', 'vendor/fixture/package_a');
    }

    public function testBadJson2()
    {
        $loader = new DiscoveryFileLoader();

        $this->expectException(JsonException::class);
        $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/error_cases/bad2.json'), 'fixture/package_a', 'vendor/fixture/package_a');
    }

    public function testEmptyJson()
    {
        $loader = new DiscoveryFileLoader();

        $assetTypes = $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/error_cases/empty.json'), 'fixture/package_a', 'vendor/fixture/package_a');

        $this->assertCount(0, $assetTypes);
    }

    public function testLoadNotExist()
    {
        $loader = new DiscoveryFileLoader();

        $this->expectException(IOException::class);
        $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/notexist.json'), 'fixture/package_a', 'vendor/fixture/package_a');
    }

}
