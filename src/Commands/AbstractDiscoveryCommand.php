<?php


namespace TheCodingMachine\Discovery\Commands;


use Composer\Command\BaseCommand;
use Composer\Factory;
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
        return $assetBuilder->findAssetTypes($localRepos);
    }
}
