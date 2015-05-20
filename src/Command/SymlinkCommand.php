<?php

namespace MyBuilder\Conductor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SymlinkCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName("symlink")
            ->setDescription('Symlinks internal packages to vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Symlink packages</info>');

        $this->conductor->symlinkPackages(getcwd());
    }
}
