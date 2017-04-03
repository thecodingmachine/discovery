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

class RemoveAssetCommandTest extends AbstractDiscoveryTest
{
    private function getInputDefinition()
    {
        return new InputDefinition([
            new InputArgument('asset-type', InputArgument::REQUIRED),
            new InputArgument('value', InputArgument::REQUIRED),
        ]);
    }

    public function testRemoveFromLocalFile()
    {
        if (file_exists('discovery.json')) {
            unlink('discovery.json');
        }

        $inputAdd = new ArrayInput([
            'asset-type' => 'foo',
            'value' => 'bar'
        ], AddAssetCommandTest::getInputDefinition());

        $result = $this->callCommand(new AddAssetCommand(), $inputAdd);

        $input = new ArrayInput([
            'asset-type' => 'foo',
            'value' => 'bar'
        ], $this->getInputDefinition());

        $result = $this->callCommand(new RemoveAssetCommand(), $input);

        $this->assertFileExists('discovery.json');
        $fileContent = file_get_contents('discovery.json');
        $this->assertEquals('[]', $fileContent);

        unlink('discovery.json');
    }

    public function testRemoveNonExistingFromProject()
    {
        if (file_exists('discovery.json')) {
            unlink('discovery.json');
        }

        $input = new ArrayInput([
            'asset-type' => 'not-exist',
            'value' => 'not-exist'
        ], $this->getInputDefinition());

        $result = $this->callCommand(new RemoveAssetCommand(), $input);

        $this->assertContains('There is no asset "not-exist" in asset type "not-exist".', $result);
    }

    public function testRemoveFromProject()
    {
        if (file_exists('discovery.json')) {
            unlink('discovery.json');
        }

        $input = new ArrayInput([
            'asset-type' => 'test-asset',
            'value' => 'a1'
        ], $this->getInputDefinition());

        $result = $this->callCommand(new RemoveAssetCommand(), $input);

        $this->assertFileExists('discovery.json');
        $fileContent = file_get_contents('discovery.json');
        $fileArray = json_decode($fileContent, true);
        $this->assertEquals(['test-asset' => [
            "value" => "a1",
            "action" => "remove"
        ]], $fileArray);

        unlink('discovery.json');
    }

}
