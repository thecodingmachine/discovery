<?php


namespace TheCodingMachine\Discovery;

class AssetTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testAssetTypeName()
    {
        $assetType = new AssetType('type');
        $this->assertSame('type', $assetType->getName());
    }

    public function testAssetTypeAdd()
    {
        $operation1 = new AssetOperation(AssetOperation::ADD, new Asset('a', 'package_a', 'dir_a', 0, []));
        $operation2 = new AssetOperation(AssetOperation::ADD, new Asset('b', 'package_b', 'dir_b', -0.5, []));
        $operation3 = new AssetOperation(AssetOperation::ADD, new Asset('c', 'package_c', 'dir_c', 0.5, []));
        $operation4 = new AssetOperation(AssetOperation::ADD, new Asset('d', 'package_d', 'dir_d', -0.5, []));
        $operation5 = new AssetOperation(AssetOperation::ADD, new Asset('e', 'package_e', 'dir_e', 0, []));

        $assetType = new AssetType('type');
        $assetType->addAssetOperation($operation1);
        $assetType->addAssetOperation($operation2);
        $assetType->addAssetOperation($operation3);
        $assetType->addAssetOperation($operation4);
        $assetType->addAssetOperation($operation5);

        $assets = $assetType->getAssets();
        $this->assertCount(5, $assets);
        $this->assertSame('c', $assets[0]->getValue());
        $this->assertSame('a', $assets[1]->getValue());
        $this->assertSame('e', $assets[2]->getValue());
        $this->assertSame('b', $assets[3]->getValue());
        $this->assertSame('d', $assets[4]->getValue());

        // Let's test again to test the "caching" of the sort.
        $assets = $assetType->getAssets();
        $this->assertCount(5, $assets);

        $assetValues = $assetType->getValues();
        $this->assertSame(['c', 'a', 'e', 'b', 'd'], $assetValues);
    }

    public function testAssetTypeJson()
    {
        $operation1 = new AssetOperation(AssetOperation::ADD, new Asset('a', 'package_a', 'dir_a', 0, []));

        $assetType = new AssetType('type');
        $assetType->addAssetOperation($operation1);

        $array = json_decode(json_encode($assetType), true);
        $this->assertSame([
            [
                'value' => 'a',
                'package' => 'package_a',
                'packageDir' => 'dir_a',
                'priority' => 0,
                'metadata' => [],
            ]
        ], $array);
    }

    public function testAssetTypeRemove()
    {
        $operation1 = new AssetOperation(AssetOperation::ADD, new Asset('a', 'package_a', 'dir_a', 0, []));
        $operation2 = new AssetOperation(AssetOperation::ADD, new Asset('b', 'package_b', 'dir_b', -0.5, []));
        $operation3 = new AssetOperation(AssetOperation::REMOVE, new Asset('a', 'package_b', 'dir_b', -0.5, []));

        $assetType = new AssetType('type');
        $assetType->addAssetOperation($operation1);
        $assetType->addAssetOperation($operation2);
        $assetType->addAssetOperation($operation3);

        $assets = $assetType->getAssets();
        $this->assertCount(1, $assets);
        $this->assertSame('b', $assets[0]->getValue());
    }
}
