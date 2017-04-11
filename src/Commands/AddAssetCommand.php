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

class AddAssetCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('discovery:add')
             ->setDescription('Add an asset')
             ->addArgument('asset-type', InputArgument::REQUIRED, 'The asset type')
             ->addArgument('value', InputArgument::REQUIRED, 'The asset to add')
             ->addOption('priority', 'p', InputOption::VALUE_REQUIRED, 'The priority', 0.0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetTypeName = $input->getArgument('asset-type');
        $assetValue = $input->getArgument('value');

        $priority = $input->getOption('priority');

        if (!is_numeric($priority)) {
            $output->writeln('<error>The priority must be a numeric value.</error>');
            return;
        }

        $loader = new DiscoveryFileLoader();

        if (file_exists('discovery.json')) {
            $assetOperationTypes = $loader->loadDiscoveryFile(new \SplFileInfo('discovery.json'), '', '');
        } else {
            $assetOperationTypes = [];
        }

        $assetOperationTypes[$assetTypeName][] = new AssetOperation(AssetOperation::ADD, new Asset($assetValue, '', '', $priority, null));

        $loader->saveDiscoveryFile($assetOperationTypes, new \SplFileObject('discovery.json', 'w+'));

        $dumper = new Dumper($this->getComposer(), $this->getIO());
        $dumper->dumpDiscoveryFiles();

        $output->writeln('discovery.json file successfully modified.');
    }
}
