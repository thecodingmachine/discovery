<?php


namespace TheCodingMachine\Discovery\Tests\Commands;


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use TheCodingMachine\Discovery\Commands\ListAssetTypesCommand;
use TheCodingMachine\Discovery\Tests\AbstractDiscoveryTest;

class ListAssetTypesCommandTest extends AbstractDiscoveryTest
{
    private function getInputDefinition()
    {
        return new InputDefinition([
            new InputArgument('asset-type', InputArgument::OPTIONAL),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt or json)', 'txt')
        ]);
    }

    public function testEmptyCall()
    {
        $input = new ArrayInput([], $this->getInputDefinition());

        $result = $this->callCommand(new ListAssetTypesCommand(), $input);

        $this->assertStringContainsString('test-asset:', $result);
        $this->assertStringContainsString('a1', $result);
        $this->assertStringContainsString('a2', $result);
    }

    public function testWithAssetType()
    {
        $input = new ArrayInput(['asset-type'=>'test-asset'], $this->getInputDefinition());

        $result = $this->callCommand(new ListAssetTypesCommand(), $input);

        $this->assertStringContainsString('test-asset:', $result);
        $this->assertStringContainsString('a1', $result);
        $this->assertStringContainsString('a2', $result);
    }

    public function testJson()
    {
        $input = new ArrayInput(['asset-type'=>'test-asset', '--format'=>'json'], $this->getInputDefinition());

        $result = $this->callCommand(new ListAssetTypesCommand(), $input);

        $this->assertJson($result);
    }


    public function testNoAssetType()
    {
        $input = new ArrayInput(['asset-type'=>'toto'], $this->getInputDefinition());

        $result = $this->callCommand(new ListAssetTypesCommand(), $input);

        $this->assertStringContainsString('Could not find the "toto" asset type.', $result);
    }
}
