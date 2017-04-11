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

class DumpCommandTest extends AbstractDiscoveryTest
{

    public function testCall()
    {
        $input = new ArrayInput([], new InputDefinition());

        $result = $this->callCommand(new DumpCommand(), $input);

        $this->assertContains('Discovery files successfully dumped in the .discovery directory.', $result);
    }

}
