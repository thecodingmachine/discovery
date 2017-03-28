<?php


namespace TheCodingMachine\Discovery\Commands;


use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;
use TheCodingMachine\Discovery\AbstractDiscoveryTest;

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

        $this->assertContains('test-asset:', $result);
        $this->assertContains('a1', $result);
        $this->assertContains('a2', $result);
    }

    public function testWithAssetType()
    {
        $input = new ArrayInput(['asset-type'=>'test-asset'], $this->getInputDefinition());

        $result = $this->callCommand(new ListAssetTypesCommand(), $input);

        $this->assertContains('test-asset:', $result);
        $this->assertContains('a1', $result);
        $this->assertContains('a2', $result);
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

        $this->assertContains('Could not find the "toto" asset type.', $result);
    }
}
