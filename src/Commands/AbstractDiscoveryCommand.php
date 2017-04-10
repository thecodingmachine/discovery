<?php


namespace TheCodingMachine\Discovery\Commands;


use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledArrayRepository;
use TheCodingMachine\Discovery\AssetsBuilder;

abstract class AbstractDiscoveryCommand extends BaseCommand
{
    /**
     * @return AssetType[]
     */
    protected function getAssetTypes() : array
    {
        $installationManager = $this->getComposer()->getInstallationManager();
        $rootDir = dirname(Factory::getComposerFile());
        $assetBuilder = new AssetsBuilder($installationManager, $this->getIO(), $rootDir);

        $localRepos = $this->getComposer()->getRepositoryManager()->getLocalRepository();

        $repos = array(
            $localRepos,
            new InstalledArrayRepository([$this->getComposer()->getPackage()]),
        );
        $compositeRepos = new CompositeRepository($repos);
        return $assetBuilder->findAssetTypes($compositeRepos);
    }
}
