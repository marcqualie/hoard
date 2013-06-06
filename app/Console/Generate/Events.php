<?php

namespace Console\Generate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Events extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('generate:events')
            ->setDescription('Generate Events for Testing');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dialog = $this->getHelperSet()->get('dialog');
        $hoard_host = $dialog->ask($output, 'Hoard Host: ', 'hoard.dev');
        $request_count = $dialog->ask($output, 'Number of Events: ', 1000);

        // Assert Fake Bucket
        $name = 'Demo Bucket';
        $appkey = '50e8d81e17466';
        $secret = sha1($appkey . 'hoard');
        $data = array(
            '_id' => $appkey,
            'name' => $name,
            'appkey' => (String) $appkey,
            'secret' => $secret,
            'roles' => array(
                'all' => 'owner'
            ),
            'created' => new \MongoDate(),
            'updated' => new \MongoDate()
        );
        $this->app->mongo->selectCollection('app')->save($data);

        // Events
        $events = array(
            'test1',
            'test2',
            'test3',
            'test4',
            'test5'
        );

        // Now pump data in
        $run = true;
        $count = 0;
        while ($run && $count < $request_count)
        {
            $event = $events[array_rand($events)];
            $post = array(
                'appkey' => $appkey,
                'format' => 'json',
                'data' => json_encode(array(
                    'random1' => rand(0, 999999),
                    'random2' => rand(0, 999999),
                    'random3' => rand(0, 999999)
                )
            ));
            $ch = curl_init('http://' . $hoard_host . '/track?event=' . $event);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
            ));
            curl_exec($ch);
            $count++;
            echo "\rCount: " . number_format($count) . '  ';
            usleep(rand(1000, 100000));
        }

    }
}
