<?php


namespace TheCodingMachine\Discovery;


use Composer\Composer;
use Composer\Installer\InstallationManager;
use Composer\Package\Package;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;

abstract class AbstractDiscoveryTest extends \PHPUnit_Framework_TestCase
{
    protected function getInstallationManagerMock(string $installPath = null) : InstallationManager
    {
        $installationManager = $this->createMock(InstallationManager::class);

        // Configure the stub.
        $installationManager->method('getInstallPath')
            ->willReturn($installPath ?? 'tests/fixtures/package_a');

        return $installationManager;
    }

    protected function getRepositoryMock() : RepositoryInterface
    {
        $packageA = new Package('package/a', '1.0.0', '1.0.0');

        $repository = $this->createMock(RepositoryInterface::class);
        $repository->method('getPackages')->willReturn([ $packageA ]);

        return $repository;
    }

    protected function getRepositoryManagerMock() : RepositoryManager
    {
        $repositoryManager = $this->createMock(RepositoryManager::class);
        $repositoryManager->method('getLocalRepository')->willReturn($this->getRepositoryMock());

        return $repositoryManager;
    }

    protected function getComposer() : Composer
    {
        $composer = new Composer();
        $composer->setInstallationManager($this->getInstallationManagerMock());
        $composer->setRepositoryManager($this->getRepositoryManagerMock());

        return $composer;
    }
}
