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
        $this->expectExceptionCode(0);
        $loader->loadDiscoveryFile(new \SplFileInfo(__DIR__.'/../fixtures/error_cases/bad.json'), 'fixture/package_a', 'vendor/fixture/package_a');
    }

    public function testBadJson2()
    {
        $loader = new DiscoveryFileLoader();

        $this->expectException(JsonException::class);
        $this->expectExceptionCode(0);
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

    public function testSaveDiscoveryFileWithOneAsset()
    {
        $loader = new DiscoveryFileLoader();

        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 0.0, null);
        $assetOperation = new AssetOperation(AssetOperation::ADD, $asset);

        $assetOperationTypes = [
            'bar' => [
                $assetOperation
            ]
        ];

        $file = new \SplFileObject('php://temp', 'w+');

        $loader->saveDiscoveryFile($assetOperationTypes, $file);
        $file->rewind();
        $json = $file->fread(4096);

        $data = json_decode($json, true);

        $this->assertSame(['bar'=>'foo'], $data);
    }

    public function testSaveDiscoveryFileWithTwoAsset()
    {
        $loader = new DiscoveryFileLoader();

        $asset = new Asset('foo', 'package/a', 'vendor/package/a', 0.0, null);
        $assetOperation = new AssetOperation(AssetOperation::ADD, $asset);

        $assetOperationTypes = [
            'bar' => [
                $assetOperation,
                $assetOperation
            ]
        ];

        $file = new \SplFileObject('php://temp', 'w+');

        $loader->saveDiscoveryFile($assetOperationTypes, $file);
        $file->rewind();
        $json = $file->fread(4096);

        $data = json_decode($json, true);

        $this->assertSame(['bar'=>['foo', 'foo']], $data);
    }
}
