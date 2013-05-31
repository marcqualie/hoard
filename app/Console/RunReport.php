<?php

namespace Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunReport extends Command
{

    protected function configure()
    {
        $this
            ->setName('report:run')
            ->setDescription('Run a report')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Report ID'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $id = $input->getArgument('id');
        $report = $this->mongo->selectCollection('report')
            ->findOne(array(
                '_id' => new \MongoId($id)
            ));
        if ( ! $report)
        {
            $this->writeln('<error>Invalid Report ID</error>');
            return false;
        }

        // Run Aggretion
        $pipeline = $report['pipeline'];
        if (empty($report['buckets']))
        {
            $report['buckets'] = array('50e8d81e17466');
        }
        $output->writeln('<info>' . $report['name'] . '</info>');
        foreach ($report['buckets'] as $bucket_id)
        {
            $bucket = $this->mongo->selectCollection('app')
                ->findOne(array(
                    'appkey' => $bucket_id
                ));
            $output->writeln('Bucket(' . $bucket_id . '): ' . $bucket['name']);
            $collection = $this->mongo->selectCollection('event_' . $bucket_id);
            $event_count = $collection->count();
            if ($event_count > 0)
            {
                $ts = new \MongoDate();
                $report_start_time = microtime(true);
                $results = $collection->aggregate($pipeline);
                if (isset($results['ok']) && (int) $results['ok'] === 1)
                {
                    $keys = array();
                    foreach ($results['result'][0] as $key => $value)
                    {
                        $keys[] = $key;
                    }
                    $data = array();
                    foreach ($results['result'] as $result)
                    {
                        $row = array();
                        foreach ($result as $key => $value)
                        {
                            $row[] = $value;
                        }
                        $data[] = $row;
                    }
                    $table = $this->getApplication()->getHelperSet()->get('table');
                    $table->setHeaders($keys);
                    $table->setRows($data);
                    $table->render($output);

                    // Save Report
                    $duration = round((microtime(true) - $report_start_time), 4);
                    $report_result = array(
                        'ts' => $ts,
                        'duration' => $duration,
                        'bucket' => $bucket_id,
                        'count' => count($data),
                        'keys' => $keys,
                        'rows' => $data
                    );
                    $this->mongo->selectCollection('report_' . $id)
                        ->insert($report_result);

                }

                // No results were returned
                else {
                    $output->writeln('<error>' . json_encode($results['result']) . '</error>');
                }
            }
        }

    }
}
