<?php

namespace MyBuilder\Conductor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CopyCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName("copy")
            ->setDescription('Copy internal packages to vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Copying packages</info>');

        $this->conductor->copyPackages(getcwd());
    }
}
