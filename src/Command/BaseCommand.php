<?php

namespace MyBuilder\Conductor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use MyBuilder\Conductor\Conductor;

abstract class BaseCommand extends Command
{
    private $workingDir;

    private $configurationFile;

    /**
     * @var Conductor
     */
    protected $conductor;

    public function __construct(Conductor $conductor)
    {
        parent::__construct();

        $this->conductor = $conductor;
    }

    protected function configure()
    {
        $this->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Configuration file', '');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->configurationFile = $this->locateConfigurationFile($input->getOption('config'));
        $this->changeWorkingDir(
            $this->workingDir = dirname($this->configurationFile)
        );

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln('<info>Using configuration:</info> ' . $this->configurationFile);
            $output->writeln('<info>Changed working dir:</info> ' . $this->workingDir);
        }
    }

    /**
     * @param string $directory
     *
     * @return string
     *
     * @throws \RuntimeException When conductor.yml is not found
     */
    private function locateConfigurationFile($directory)
    {
        $directory = realpath($directory);

        if (false !== $directory) {
            if (file_exists($directory . '/conductor.yml')) {
                return $directory . '/conductor.yml';
            }
        }

        throw new \RuntimeException('Cannot find the conductor.yml configuration file');
    }

    /**
     * @return mixed
     */
    protected function getConfiguration()
    {
        $yaml = new Parser();
        
        return $yaml->parse(file_get_contents($this->configurationFile));
    }

    /**
     * @param string $workingDir
     *
     * @return string
     */
    protected function changeWorkingDir($workingDir)
    {
        chdir($workingDir);
    }
}
