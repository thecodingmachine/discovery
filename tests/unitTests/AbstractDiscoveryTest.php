<?php


namespace TheCodingMachine\Discovery;


use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use TheCodingMachine\Discovery\Commands\ListAssetTypesCommand;

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
        $composer->setPackage(new RootPackage('__root__', 'stable', '1.0'));

        return $composer;
    }

    protected function getIO() : IOInterface
    {
        $io = $this->createMock(IOInterface::class);
        $io->method('writeError');

        return $io;
    }

    /**
     * Calls the command passed in parameter. Returns the output.
     *
     * @param BaseCommand $command
     * @param InputInterface $input
     * @return string
     */
    protected function callCommand(BaseCommand $command, InputInterface $input) : string
    {
        $command->setComposer($this->getComposer());
        $command->setIO($this->getIO());

        $output = new BufferedOutput();

        $r = new \ReflectionMethod($command, 'execute');
        $r->setAccessible(true);
        $r->invoke($command, $input, $output);

        return $output->fetch();
    }

}
