<?php

namespace Console\System;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Model\Bucket;

class UpgradeLegacyBuckets extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('system:upgrade-legacy-buckets')
            ->setDescription('Upgrade legacy buckets')
            ->addOption(
                'appkey',
                'Appkey'
//                InputOption::OPTIONAL
            )
            ;
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Confirm that this may break the system and lose data
        $dialog = $this->getHelperSet()->get('dialog');
        $output->writeln('<fg=yellow;options=bold>This will upgrade all legacy buckets; Data may lost!!</fg=yellow;options=bold>');
//        if ( ! $dialog->askConfirmation($output, 'Continue? ', false))
//        {
//            return;
//        }

        // Limit to one bucket (jsut to be safe)
        $collection = $this->mongo->selectCollection(Bucket::$collection);
        $buckets = array();
        if ($input->getOption('appkey')) {
            $buckets = $collection->find(array('appkey' => $input->getOption('appkey')));
        } else {
            $buckets = $collection->find(array('appkey' => array('$exists' => true)));
        }

        // Configuration
        foreach ($buckets as $bucket1) {
            $old_id = $bucket1['_id'];
            echo 'Checking: ' . $old_id . ' ... ' . PHP_EOL;
            if (is_object($old_id) && get_class($old_id) === 'MongoId' && !empty($bucket1['appkey'])) {
                echo ' - upgraeding ...' . PHP_EOL;
                $new_id = $bucket1['appkey'];
                $data = array(
                    '_id' => (String) $new_id,
                    'description' => isset($bucket1['name']) ? $bucket1['name'] : $new_id,
                    'roles' => $bucket1['roles'],
                    'created' => isset($bucket1['created']) ? $bucket1['created'] : 0,
                    'updated' => new \MongoDate()
                );
//                continue;
                $insert = $collection->insert($data);
                if (isset($insert['ok']) && (int) $insert['ok'] === 1) {
                    $this->mongo->selectCollection('app_legacy')->save($bucket1);
                    $collection->remove(array('_id' => $old_id));
                }
            } else {
                echo ' - upgrade skipped' . PHP_EOL;
            }
        }

        // Installation Complete
        $output->writeln(' ');
        $output->writeln('<info>Installation Complete</info>');

    }
}
