<?php
require 'vendor/autoload.php';

use Aws\Rds\RdsClient; 
use Aws\Exception\AwsException;

//Create a RDSClient
$rdsClient = new Aws\Rds\RdsClient([
  'profile' => 'default',
  'region'  => 'eu-central-1',
  'version' => 'latest'
]);

$dbIdentifier = 'my-db';
$dbClass = 'db.t2.micro';
$storage = 5;
$engine = 'MySQL';
$username = 'username';
$password =  'password';

try {
    $result = $rdsClient->createDBInstance([
        'DBInstanceIdentifier' => $dbIdentifier,
        'DBInstanceClass' => $dbClass ,
        'AllocatedStorage' => $storage,
        'Engine' => $engine,
        'MasterUsername' => $username,
        'MasterUserPassword' => $password,
    ]);
    var_dump($result);
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo "\n";
} 