<?php


namespace TheCodingMachine\Discovery;

use Composer\Installer\InstallationManager;
use Composer\IO\BufferIO;
use Composer\Package\Package;
use Composer\Repository\RepositoryInterface;

class AssetsBuilderTest extends AbstractDiscoveryTest
{
    public function testAssetBuilder()
    {
        $installationManager = $this->getInstallationManagerMock();

        $packageA = new Package('package/a', '1.0.0', '1.0.0');

        $io = new BufferIO();

        $assetsBuilder = new AssetsBuilder($installationManager, $io, '.');
        $assetTypes = $assetsBuilder->buildAssetTypes([ $packageA ]);

        $this->assertCount(1, $assetTypes);
        $this->assertArrayHasKey('test-asset', $assetTypes);
        $this->assertEmpty($io->getOutput());
    }

    public function testAssetBuilderErrorHandling()
    {
        $installationManager = $this->getInstallationManagerMock('tests/fixtures/package_error');

        $packageA = new Package('package/a', '1.0.0', '1.0.0');

        $io = new BufferIO();

        $assetsBuilder = new AssetsBuilder($installationManager, $io, '.');
        $assetTypes = $assetsBuilder->buildAssetTypes([ $packageA ]);

        $this->assertCount(0, $assetTypes);
        $this->assertNotEmpty($io->getOutput());
    }

    public function testFindAssetTypes()
    {

        $installationManager = $this->getInstallationManagerMock();


        $io = new BufferIO();

        $assetsBuilder = new AssetsBuilder($installationManager, $io, '.');

        $repository = $this->getRepositoryMock();

        $assetTypes = $assetsBuilder->findAssetTypes($repository);

        $this->assertCount(1, $assetTypes);
    }
}
