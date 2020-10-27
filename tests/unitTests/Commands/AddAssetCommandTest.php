<?php


namespace TheCodingMachine\Discovery\Tests\Commands;


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use TheCodingMachine\Discovery\Commands\AddAssetCommand;
use TheCodingMachine\Discovery\Tests\AbstractDiscoveryTest;

class AddAssetCommandTest extends AbstractDiscoveryTest
{
    public static function getInputDefinition()
    {
        return new InputDefinition([
            new InputArgument('asset-type', InputArgument::REQUIRED),
            new InputArgument('value', InputArgument::REQUIRED),
            new InputOption('priority', 'p', InputOption::VALUE_REQUIRED, '', 0.0)
        ]);
    }

    public function testCall()
    {
        if (file_exists('discovery.json')) {
            unlink('discovery.json');
        }

        $input = new ArrayInput([
            'asset-type' => 'foo',
            'value' => 'bar'
        ], self::getInputDefinition());

        $result = $this->callCommand(new AddAssetCommand(), $input);

        $this->assertFileExists('discovery.json');

        $result = $this->callCommand(new AddAssetCommand(), $input);

        $this->assertFileExists('discovery.json');


        unlink('discovery.json');
    }

    public function testCallWithBadPriority()
    {
        $input = new ArrayInput([
            'asset-type' => 'foo',
            'value' => 'bar',
            '--priority' => 'a'
        ], self::getInputDefinition());

        $result = $this->callCommand(new AddAssetCommand(), $input);

        $this->assertStringContainsString('The priority must be a numeric value.', $result);

    }

}
