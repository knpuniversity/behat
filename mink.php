<?php

require __DIR__.'/vendor/autoload.php';

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;

// Important object #1
$driver = new GoutteDriver();

// Important object #2
$session = new Session($driver);

$session->start();
$session->visit('http://jurassicpark.wikia.com');

echo "Status code: ". $session->getStatusCode() . "\n";
echo "Current URL: ". $session->getCurrentUrl() . "\n";
