<?php

namespace Console\Generate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Console\Command;
use Model\Bucket;

class Events extends Command
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
        $request_count = $dialog->ask($output, 'Number of Events: ', 10000);

        // Assert Fake Bucket
        $id = '51cd6e83c2cea';
        $alias = 'demo-bucket';
        $data = array(
            '_id' => $id,
            'alias' => array(
                $alias
            ),
            'description' => 'Demo Bucket',
            'roles' => array(
                'all' => 'owner'
            ),
            'created' => new \MongoDate(),
            'updated' => new \MongoDate()
        );
        $bucket = Bucket::create($data);

        // Events
        $events = array(
            'test1',
            'test2',
            'test3',
            'test4',
            'test5'
        );

        // Get Faker Factory
        $faker = \Faker\Factory::create();

        // Now pump data in
        $run = true;
        $count = 0;
        while ($run && $count < $request_count)
        {
            $event = $events[array_rand($events)];
            $post = json_encode(array(
                'v' => 1,
                'b' => $alias,
                'e' => $event,
                'd' => array(
                    'name' => $faker->name,
                    'gender' => rand(0, 1) === 1 ? 'male' : 'female',
                    'country' => $faker->countryCode,
                    'response_time' => rand(1, 1200)
                )
            ));
            $ch = curl_init('http://' . $hoard_host . '/api/track?apikey=' . $this->apikey);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post
            ));
            $response = curl_exec($ch);
            $count++;
            echo "\rCount: " . str_pad(number_format($count), 10, ' ', STR_PAD_RIGHT) . '  ' . $response . '  ';
            usleep(rand(100, 10000));
        }

    }
}
