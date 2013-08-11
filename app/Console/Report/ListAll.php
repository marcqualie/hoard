<?php

namespace Console\Report;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAll extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('report:list')
            ->setDescription('Run a report');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $reports = $this->mongo->selectCollection('report')
            ->find()
            ->sort(array(
                'name' => 1
            ));
        foreach ($reports as $report) {
            $output->writeln('<info>' . $report['_id'] . '</info> ' . $report['name']);
        }
    }
}
