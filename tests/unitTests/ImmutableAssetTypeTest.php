<?php


namespace TheCodingMachine\Discovery;

class ImmutableAssetTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $array = [
            [
                'value' => 'a1',
                'package' => 'package/a',
                'packageDir' => 'vendor/package/a',
                'priority' => 0.0,
                'metadata' => []
            ]
        ];
        $assetType = ImmutableAssetType::fromArray('name', $array);

        $this->assertSame($assetType->getName(), 'name');
        $this->assertCount(1, $assetType->getAssets());
        $asset = $assetType->getAssets()[0];
        $this->assertSame('a1', $asset->getValue());
        $this->assertSame('package/a', $asset->getPackage());
        $this->assertSame('vendor/package/a', $asset->getPackageDir());
        $this->assertSame(0.0, $asset->getPriority());
        $this->assertSame([], $asset->getMetadata());
    }
}
