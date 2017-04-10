<?php


namespace TheCodingMachine\Discovery;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\InstalledArrayRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class in charge of generating the .discovery directory with the Discovery class and files.
 */
class Dumper
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function dumpDiscoveryFiles()
    {
        $fileSystem = new Filesystem();

        $localRepos = $this->composer->getRepositoryManager()->getLocalRepository();
        $repos = array(
            $localRepos,
            new InstalledArrayRepository([clone $this->composer->getPackage()]),
        );
        $compositeRepos = new CompositeRepository($repos);
        $assetTypes = $this->getAssetsBuilder()->findAssetTypes($compositeRepos);

        // Let's get an array of values, indexed by asset type (to store in the discovery_values.php file)
        $values = array_map(function (AssetType $assetType) {
            return $assetType->getValues();
        }, $assetTypes);

        $fileSystem->dumpFile('.discovery/discovery_values.php', '<?php
return '.var_export($values, true).";\n");

        // Let's get an array of assetTypes, indexed by asset type (to store in the discovery_asset_types.php file)
        $assetTypes = array_map(function (AssetType $assetType) {
            return $assetType->jsonSerialize();
        }, $assetTypes);

        $fileSystem->dumpFile('.discovery/discovery_asset_types.php', '<?php
return '.var_export($assetTypes, true).";\n");

        // Let's copy the Discovery class in the .discovery directory. This is needed because otherwise, we have no way to "find" the .discovery directory easily.
        $fileSystem->dumpFile('.discovery/Discovery.php', file_get_contents(__DIR__.'/Discovery.php.tpl'));
    }

    /**
     * @return AssetsBuilder
     */
    private function getAssetsBuilder() : AssetsBuilder
    {
        $installationManager = $this->composer->getInstallationManager();
        $rootDir = dirname(Factory::getComposerFile());
        return new AssetsBuilder($installationManager, $this->io, $rootDir);
    }
}