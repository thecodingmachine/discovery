<?php


namespace TheCodingMachine\Discovery;

use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Repository\RepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
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
    /**
     * @var string
     */
    private $rootDir;

    public function __construct(InstallationManager $installationManager, IOInterface $io, string $rootDir)
    {
        $this->installationManager = $installationManager;
        $this->io = $io;
        $this->rootDir = $rootDir;
    }

    /**
     * Find discovery.json files in the passed repository and builds an asset type.
     *
     * @param RepositoryInterface $repository
     * @param RootPackageInterface $rootPackage
     * @return array|AssetType[]
     */
    public function findAssetTypes(RepositoryInterface $repository) : array
    {
        $unorderedPackagesList = $repository->getPackages();

        /**
         * Package with type `metapackage` doesn't have installation path, which
         * brings with it a problem finding discovery JSON files in all
         * packages. This filter removes "metapackages" and leaves only real.
         *
         * Documentation: https://getcomposer.org/doc/04-schema.md#type
         */
        $realPackagesList = array_filter(
            $unorderedPackagesList,
            function (PackageInterface $package) {
                return 'metapackage' !== $package->getType();
            }
        );

        // For some weird reason, some packages can be in double in the repository.
        // This has been observed when doing a "composer install" on an empty vendor directory.
        // Let's ensure each package is represented only once.
        $uniquePackages = [];
        foreach($realPackagesList as $package) {
            $uniquePackages[$package->getName()] = $package;
        }
        $uniquePackages = array_values($uniquePackages);

        $orderedPackageList = PackagesOrderer::reorderPackages($uniquePackages);

        $packages = array_filter($orderedPackageList, function (PackageInterface $package) {
            $packageInstallPath = $this->getInstallPath($package);

            return file_exists($packageInstallPath.'/discovery.json');
        });

        return $this->buildAssetTypes($packages);
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
        $packageInstallPath = $this->getInstallPath($package);

        $fileSystem = new Filesystem();

        $packageDir = $fileSystem->makePathRelative(realpath($packageInstallPath), realpath($this->rootDir));

        $path = $packageInstallPath.'/discovery.json';

        $discoveryFileLoader = new DiscoveryFileLoader();

        return $discoveryFileLoader->loadDiscoveryFile(new \SplFileInfo($path), $package->getName(), $packageDir);
    }

    private function getInstallPath(PackageInterface $package) : string
    {
        if ($package instanceof RootPackageInterface) {
            return getcwd();
        } else {
            return $this->installationManager->getInstallPath($package);
        }
    }
}
