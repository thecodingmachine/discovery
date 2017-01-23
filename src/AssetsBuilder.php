<?php


namespace TheCodingMachine\Discovery;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use TheCodingMachine\Discovery\Utils\JsonException;

/**
 * Transforms discovery.json files into arrays of assets.
 */
class AssetsBuilder
{


    /**
     * @var InstallationManager
     */
    private $installationManager;
    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(InstallationManager $installationManager, IOInterface $io)
    {
        $this->installationManager = $installationManager;
        $this->io = $io;
    }


    /**
     * Builds the AssetTypes that will be exported in the generated TheCodingMachine\Discovery class.
     *
     * @param PackageInterface[] $discoveryPackages
     *
     * @return AssetType[] An array of AssetType, indexed by asset type name.
     */
    public function buildAssetTypes(array $discoveryPackages) : array
    {
        /* @var $assetTypes AssetType[] */
        $assetTypes = [];

        foreach ($discoveryPackages as $package) {
            try {
                $assetOperationsByType = $this->getDiscoveryJson($package);
                // TODO display warnings if problem with discovery.json without crashing!

                foreach ($assetOperationsByType as $type => $assetOperations) {
                    $assetTypes[$type] = $assetTypes[$type] ?? new AssetType($type);

                    foreach ($assetOperations as $assetOperation) {
                        $assetTypes[$type]->addAssetOperation($assetOperation);
                    }
                }
            } catch (JsonException $exception) {
                $this->io->writeError($exception->getMessage());
            }
        }

        return $assetTypes;
    }

    /**
     * Returns the parsed JSON of the discovery.json file of a package.
     *
     * @param PackageInterface $package
     *
     * @return AssetOperation[][]
     *
     * @throws \TheCodingMachine\Discovery\Utils\JsonException
     */
    private function getDiscoveryJson(PackageInterface $package) : array
    {
        $packageInstallPath = $this->installationManager->getInstallPath($package);

        $path = $packageInstallPath.'/discovery.json';

        $discoveryFileLoader = new DiscoveryFileLoader();

        return $discoveryFileLoader->loadDiscoveryFile(new \SplFileInfo($path), $package->getName(), $path);
    }

}