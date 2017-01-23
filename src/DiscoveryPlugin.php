<?php

declare (strict_types = 1);

namespace TheCodingMachine\Discovery;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use TheCodingMachine\Discovery\Utils\FileSystem;
use TheCodingMachine\Discovery\Utils\JsonException;

class DiscoveryPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Composer
     */
    protected $composer;
    protected $io;

    /**
     * Apply plugin modifications to Composer.
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => 'beforeDumpAutoload',
        ];
    }

    /**
     * @return AssetsBuilder
     */
    private function getAssetsBuilder() : AssetsBuilder
    {
        $installationManager = $this->composer->getInstallationManager();
        return new AssetsBuilder($installationManager, $this->io);
    }

    public function beforeDumpAutoload(Event $event)
    {
        // Plugin has been uninstalled
        if (!file_exists(__FILE__)) {
            return;
        }

        $fileSystem = new FileSystem();

        $discoveryPackages = $this->getDiscoveryPackages();
        $assetTypes = $this->getAssetsBuilder()->buildAssetTypes($discoveryPackages);

        // Let's get an array of values, indexed by asset type (to store in the discovery_values.php file)
        $values = array_map(function(AssetType $assetType) {
            return $assetType->getValues();
        }, $assetTypes);

        $fileSystem->dumpFile('.discovery/discovery_values.php', '<?php
return '.var_export($values, true).";\n");

        // Let's get an array of assetTypes, indexed by asset type (to store in the discovery_asset_types.php file)
        $assetTypes = array_map(function(AssetType $assetType) {
            return $assetType->jsonSerialize();
        }, $assetTypes);

        $fileSystem->dumpFile('.discovery/discovery_asset_types.php', '<?php
return '.var_export($assetTypes, true).";\n");

        // Let's copy the Discovery class in the .discovery directory. This is needed because otherwise, we have no way to "find" the .discovery directory easily.
        $fileSystem->dumpFile('.discovery/Discovery.php', file_get_contents(__DIR__.'/Discovery.php.tpl'));

        $this->registerClassInAutoloader();
    }

    /**
     * Returns the list of packages containing a "discovery.json" file in the root directory.
     *
     * Packages are ordered by dependencies.
     *
     * @return PackageInterface[]
     */
    private function getDiscoveryPackages()
    {
        $localRepos = $this->composer->getRepositoryManager()->getLocalRepository();
        $unorderedPackagesList = $localRepos->getPackages();

        $orderedPackageList = PackagesOrderer::reorderPackages($unorderedPackagesList);

        return array_filter($orderedPackageList, function (PackageInterface $package) {
            $installationManager = $this->composer->getInstallationManager();

            $packageInstallPath = $installationManager->getInstallPath($package);

            return file_exists($packageInstallPath.'/discovery.json');
        });
    }



    /**
     * This registers the generated TheCodingMachine\Discovery class in the autoloader.
     */
    private function registerClassInAutoloader()
    {
        // Let's dynamically add a Discovery file to the autoloader
        $discoveryFile = '.discovery/Discovery.php';
        $autoload = $this->composer->getPackage()->getAutoload();
        $autoload['classmap'][] = $discoveryFile;
        $this->composer->getPackage()->setAutoload($autoload);
    }
}
