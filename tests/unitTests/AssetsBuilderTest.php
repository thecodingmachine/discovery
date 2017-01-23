<?php


namespace TheCodingMachine\Discovery;

use Composer\Installer\InstallationManager;
use Composer\IO\BufferIO;
use Composer\Package\Package;

class AssetsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testAssetBuilder()
    {
        // Create a stub for the SomeClass class.
        $installationManager = $this->createMock(InstallationManager::class);

        // Configure the stub.
        $installationManager->method('getInstallPath')
            ->willReturn('tests/fixtures/package_a');

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
        // Create a stub for the SomeClass class.
        $installationManager = $this->createMock(InstallationManager::class);

        // Configure the stub.
        $installationManager->method('getInstallPath')
            ->willReturn('tests/fixtures/package_error');

        $packageA = new Package('package/a', '1.0.0', '1.0.0');

        $io = new BufferIO();

        $assetsBuilder = new AssetsBuilder($installationManager, $io, '.');
        $assetTypes = $assetsBuilder->buildAssetTypes([ $packageA ]);

        $this->assertCount(0, $assetTypes);
        $this->assertNotEmpty($io->getOutput());
    }
}
