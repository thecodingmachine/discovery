<?php


namespace TheCodingMachine\Discovery\Commands;


use Composer\Command\BaseCommand;
use Composer\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheCodingMachine\Discovery\Asset;
use TheCodingMachine\Discovery\AssetOperation;
use TheCodingMachine\Discovery\AssetsBuilder;
use TheCodingMachine\Discovery\AssetType;
use TheCodingMachine\Discovery\DiscoveryFileLoader;
use TheCodingMachine\Discovery\Dumper;

class RemoveAssetCommand extends AbstractDiscoveryCommand
{
    protected function configure()
    {
        $this->setName('discovery:remove')
             ->setDescription('Remove an asset')
             ->addArgument('asset-type', InputArgument::REQUIRED, 'The asset type')
             ->addArgument('value', InputArgument::REQUIRED, 'The asset to remove');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetTypeName = $input->getArgument('asset-type');
        $assetValue = $input->getArgument('value');

        $loader = new DiscoveryFileLoader();

        if (file_exists('discovery.json')) {
            $assetOperationTypes = $loader->loadDiscoveryFile(new \SplFileInfo('discovery.json'), '', '');
        } else {
            $assetOperationTypes = [];
        }

        if ($this->isAssetInLocalFile($assetOperationTypes, $assetTypeName, $assetValue)) {
            $assetOperationTypes[$assetTypeName] = array_filter($assetOperationTypes[$assetTypeName], function(AssetOperation $assetOperation) use ($assetValue) {
                return !($assetOperation->getOperation() === AssetOperation::ADD && $assetOperation->getAsset()->getValue() === $assetValue);
            });

            if (empty($assetOperationTypes[$assetTypeName])) {
                unset($assetOperationTypes[$assetTypeName]);
            }

        } else {
            // Does this value exists in the first place?

            $assetTypes = $this->getAssetTypes();

            if (!$this->isAssetInProject($assetTypes, $assetTypeName, $assetValue)) {
                $output->writeln(sprintf('<error>There is no asset "%s" in asset type "%s".</error>', $assetValue, $assetTypeName));
                return;
            }

            $assetOperationTypes[$assetTypeName][] = new AssetOperation(AssetOperation::REMOVE, new Asset($assetValue, '', '', 0.0, null));
        }


        $loader->saveDiscoveryFile($assetOperationTypes, new \SplFileObject('discovery.json', 'w+'));

        $dumper = new Dumper($this->getComposer(), $this->getIO());
        $dumper->dumpDiscoveryFiles();

        $output->writeln('discovery.json file successfully modified.');
    }

    /**
     * @param AssetOperation[][] $assetOperationTypes
     * @param string $assetTypeName
     * @param string $assetValue
     * @return bool
     */
    private function isAssetInLocalFile(array $assetOperationTypes, string $assetTypeName, string $assetValue) : bool
    {
        if (!isset($assetOperationTypes[$assetTypeName])) {
            return false;
        }

        foreach ($assetOperationTypes[$assetTypeName] as $assetOperation) {
            if ($assetOperation->getOperation() === AssetOperation::ADD && $assetOperation->getAsset()->getValue() === $assetValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AssetType[] $assetTypes
     * @param string $assetTypeName
     * @param string $assetValue
     * @return bool
     */
    private function isAssetInProject(array $assetTypes, string $assetTypeName, string $assetValue) : bool
    {
        if (!isset($assetTypes[$assetTypeName])) {
            return false;
        }

        foreach ($assetTypes[$assetTypeName]->getAssets() as $asset) {
            if ($asset->getValue() === $assetValue) {
                return true;
            }
        }

        return false;
    }
}
