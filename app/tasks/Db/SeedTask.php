<?php

namespace Db;

use Phalcon\CLI\Task;
use User;

class SeedTask extends Task
{

    public function mainAction ()
    {

        echo "Seeding Database\n";

        // Drop databases
        $user = new User;
        $collection = $user->getConnection()->selectCollection($user->getSource());
        $collection->drop();

        $this->seedUsers();

    }

    /**
     * Seed users
     */
    protected function seedUsers()
    {
        $user = new User;
        $user->name = 'Admin User';
        $user->username = 'admin';
        $user->email = 'admin@example.org';
        $user->password = password_hash('password', PASSWORD_BCRYPT, ['cost' => 13]);
        $user->save();
    }

}
