<?php

namespace Console\System;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('system:setup')
            ->setDescription('Initial System Setup and Configuration');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<error>Please refer to the manual for setup instructions</error>');
    }
}
