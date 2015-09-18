<?php

require __DIR__.'/vendor/autoload.php';

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;

//$driver = new GoutteDriver();
$driver = new Selenium2Driver();
$session = new Session($driver);

$session->start();
$session->visit('http://jurassicpark.wikia.com');

//echo "The status code is ".$session->getStatusCode()."\n";
echo "The current URL is ".$session->getCurrentUrl()."\n";

// Hallo! I'm a DocumentElement
$page = $session->getPage();

echo "The start of the page text is ".substr($page->getText(), 0, 56)."\n";

// And I'm a NodeElement!
$nodeElement = $page->find('css', '.subnav-2 li a');
echo "The matched link text is ".$nodeElement->getText()."\n";

$randomLink = $page->findLink('Random page');

echo "The matched URL is ".$randomLink->getAttribute('href')."\n";

$randomLink->click();

echo "The new URL is ".$session->getCurrentUrl()."\n";

$session->stop();