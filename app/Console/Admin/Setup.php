<?php

namespace Console\Admin;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('admin:setup')
            ->setDescription('Setup system ready for use');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dialog = $this->getHelperSet()->get('dialog');
        $email = $dialog->ask($output, 'Email Address: ', 'admin@example.com');
        $password = $dialog->ask($output, 'Password: ', '');
    }
}
