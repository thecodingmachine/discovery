<?php


namespace TheCodingMachine\Discovery\Commands;


use Composer\Command\BaseCommand;
use Composer\Factory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheCodingMachine\Discovery\AssetsBuilder;
use TheCodingMachine\Discovery\AssetType;
use TheCodingMachine\Discovery\Dumper;

class DumpCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('discovery:dump')
             ->setDescription('Regenerates the Discovery class (in the .discovery folder). This is automatically triggered on install and update.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dumper = new Dumper($this->getComposer(), $this->getIO());
        $dumper->dumpDiscoveryFiles();

        $output->writeln('Discovery files successfully dumped in the .discovery directory.');
    }
}
