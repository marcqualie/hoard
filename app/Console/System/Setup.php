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
            ->setAliases(array(
                'install'
            ))
            ->setDescription('Initial System Setup and Configuration');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Confirm that this may break the system and lose data
        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln('<fg=yellow;options=bold>This will create a fresh install; Data may lost.</fg=yellow;options=bold>');
        if ( ! $dialog->askConfirmation($output, 'Continue? ', false))
        {
            return;
        }

        // Configuration
        // TODO: Update mongodb configuraiton here

        // Create User
        $output->writeln(' ');
        if ($dialog->askConfirmation($output, 'Would you like to create an admin user? ', false))
        {
            $output->writeln('<info>User Creation</info>');
            $this->getApplication()->find('user:create')->run($input, $output);
        }

        // Installation Complete
        $output->writeln(' ');
        $output->writeln('<info>Installation Complete</info>');

    }
}
