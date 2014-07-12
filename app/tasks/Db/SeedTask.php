<?php

namespace Db;

use Bucket;
use Event;
use MongoDate;
use MongoId;
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
        $user->_id = new MongoId('53c196fe06000e34580041a0');
        $user->name = 'Admin User';
        $user->username = 'admin';
        $user->email = 'admin@example.org';
        $user->password = password_hash('password', PASSWORD_BCRYPT, ['cost' => 13]);
        $user->save();

        // Create demo buckets
        $buckets = [];
        for ($i = 0; $i < 5; $i++) {
            $bucket = new Bucket;
            $bucket->_id = new MongoId('53c1864406000e024a0041a' . $i);
            $bucket->name = 'Demo Bucket ' . ($i + 1);
            $bucket->description = 'This bucket is created automaitcally for demo purposes';
            $bucket->roles = [
                (string) $user->getId() => 'admin'
            ];
            $bucket->save();
            $buckets[$i] = $bucket;
        }

        // Events
        for ($i = 0; $i < 50; $i++) {
            $event = new Event;
            $event->name = 'ping';
            $event->bucket_id = ceil($i / count($buckets));
            $event->data = [
                'int' => 1,
                'string' => 'blah blah',
                'date' => new MongoDate(),
            ];
            $event->save();
        }

    }

}
