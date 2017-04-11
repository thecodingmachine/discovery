<?php


namespace TheCodingMachine\Discovery\Commands;


use Symfony\Component\Console\Command\Command;

class CommandProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandProvider()
    {
        $commandProvider = new CommandProvider();
        $commands = $commandProvider->getCommands();
        $this->assertContainsOnlyInstancesOf(Command::class, $commands);
    }
}
