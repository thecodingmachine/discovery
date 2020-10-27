<?php


namespace TheCodingMachine\Discovery\Tests\Commands;


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use TheCodingMachine\Discovery\Commands\DumpCommand;
use TheCodingMachine\Discovery\Tests\AbstractDiscoveryTest;

class DumpCommandTest extends AbstractDiscoveryTest
{

    public function testCall()
    {
        $input = new ArrayInput([], new InputDefinition());

        $result = $this->callCommand(new DumpCommand(), $input);

        $this->assertStringContainsString('Discovery files successfully dumped in the .discovery directory.', $result);
    }

}
