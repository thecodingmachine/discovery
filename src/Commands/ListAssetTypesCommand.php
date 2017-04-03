<?php


namespace TheCodingMachine\Discovery\Commands;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheCodingMachine\Discovery\AssetType;

class ListAssetTypesCommand extends AbstractDiscoveryCommand
{
    protected function configure()
    {
        $this->setName('discovery:list')
             ->setDescription('List the values of the asset type passed in parameter (or list all asset types if no parameter is passed)')
             ->addArgument('asset-type', InputArgument::OPTIONAL, 'The asset type to list')
             ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt or json)', 'txt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetTypes = $this->getAssetTypes();

        $assetTypeName = $input->getArgument('asset-type');
        if ($assetTypeName) {
            if (!isset($assetTypes[$assetTypeName])) {
                $output->writeln(sprintf('<error>Could not find the "%s" asset type.</error>', $assetTypeName));
                return;
            }
            $assetTypes = [ $assetTypes[$assetTypeName] ];
        }

        switch ($input->getOption('format')) {
            case 'txt':
                $this->renderTxtAssetTypes($assetTypes, $output);
                break;
            case 'json':
                $output->writeln(json_encode($assetTypes, JSON_PRETTY_PRINT));
                break;
            default:
                // @codeCoverageIgnoreStart
                throw new \RuntimeException('Unexpected format');
                // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param AssetType[] $assetTypes
     * @param OutputInterface $output
     */
    protected function renderTxtAssetTypes(array $assetTypes, OutputInterface $output)
    {
        foreach ($assetTypes as $assetType) {
            $output->writeln('<info>'.$assetType->getName().':</info>');
            $values = $assetType->getValues();
            if (empty($values)) {
                $output->writeln('<comment>! No values for this asset type</comment>');
            } else {
                foreach ($assetType->getAssets() as $asset) {
                    $output->writeln('  '.$asset->getValue());
                    $output->writeln('    <comment>Priority: '.$asset->getPriority().'</comment>', OutputInterface::VERBOSITY_VERBOSE);
                    $output->writeln('    <comment>Package: '.$asset->getPackage().'</comment>', OutputInterface::VERBOSITY_VERBOSE);
                    $output->writeln('    <comment>Package directory: '.$asset->getPackageDir().'</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
                    $output->writeln('    <comment>Metadata: '.json_encode($asset->getMetadata()).'</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
                }
            }
        }
    }
}
