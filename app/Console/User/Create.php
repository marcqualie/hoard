<?php

namespace Console\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends \Console\Command
{

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Setup system ready for use');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $collection = $this->app->mongo->selectCollection('user');

        $dialog = $this->getHelperSet()->get('dialog');
        while (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $dialog->ask($output, 'Email Address: ');
        }

        // Create admin user in Mongo
        $email = trim(strtolower($email));
        $user = $collection->findOne(array(
            'email' => $email
        ));
        if (isset($user['_id'])) {
            $output->writeln('<error>This user already exists</error>');

            return 1;
        }

        // Ask for password
        while (empty($password)) {
            $password = $dialog->askHiddenResponse($output, 'Password: ');
        }
        while (empty($confirm_password)) {
            $confirm_password = $dialog->askHiddenResponse($output, 'Confirm:  ');
        }
        if ($password !== $confirm_password) {
            $output->writeln('<error>Passwords do not match.</error>');

            return 1;
        }

        // Should they be admin
        $admin = $dialog->ask($output, 'Grant Admin Permission?: ') === 'y' ? 1 : 0;

        // Generate Keys for API Access
        $token = uniqid();
        $apikey = sha1(uniqid() . uniqid());
        $secret = sha1(uniqid() . uniqid());

        // Build User Data
        $user = array(
            'email' => $email,
            'password' => \Hoard\Auth::password($password),
            'token' => $token,
            'credentials' => array(
                array(
                    'apikey' => $apikey,
                    'secret' => $secret,
                    'created' => new \MongoDate()
                )
            ),
            'admin' => $admin,
            'created' => new \MongoDate(),
            'updated' => new \MongoDate()
        );
        $collection->insert($user);
        $output->writeln('User created: 1');

    }
}
