<?php

use MongoDB\Client as MongoDBClient;


require 'vendor/autoload.php';

$username = getenv('MONGO_USERNAME');
$password = getenv('MONGO_PASSWORD');

if (($username === false) || ($password === false)) {
    exit("Please provide username and password in your .env file.");
};


try{
    $mongo = new MongoDBClient(
        'mongodb://mongo:27017',
        [
            'username' => $username,
            'password' => $password,
        ],
    );
    
    
    $database = $mongo->test1;
    
    $database->createCollection('people', [
        'validator' => [
            'id_number' => ['$type' => 'string', '$regex' => "^[0-9]{13}$"],
            'first_name' => ['$type' => 'string', '$regex' => '^[A-Za-z]+$'],
            'last_name' => ['$type' => 'string', '$regex' => '^[A-Za-z]+$'],
            'date_of_birth' => ['$type' => 'string', '$regex' => '^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$'],
        ]
    ]);

    $people = $database->people;

    $people->createIndex(['id_number' => 1], ['unique' => true]);

    echo "Operation complete! database initialization successful 🚀 .\n";
} catch(Exception $e){
    echo "Operation failed! database initialization failed.\n";
    echo $e;
}
