<?php

namespace Db;

use Bucket;
use Phalcon\CLI\Task;
use User;

class SeedTask extends Task
{

    public function mainAction ()
    {

        echo "Seeding Database\n";

        // Drop databases
        foreach (['User', 'Bucket'] as $model_name) {
            $model = new $model_name;
            $collection = $model->getConnection()->selectCollection($model->getSource());
            $collection->drop();
        }

        // Create Admin
        $user = new User;
        $user->name = 'Admin User';
        $user->username = 'admin';
        $user->email = 'admin@example.org';
        $user->password = password_hash('password', PASSWORD_BCRYPT, ['cost' => 13]);
        $user->save();

        // Create demo bucket
        $bucket = new Bucket;
        $bucket->name = 'Demo Bucket';
        $bucket->description = 'This bucket is created automaitcally for demo purposes';
        $bucket->roles = [
            (string) $user->getId() => 'admin'
        ];
        $bucket->save();

    }

}
