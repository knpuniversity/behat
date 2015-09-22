<?php

require __DIR__.'/vendor/autoload.php';

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;

// Important object #1
//$driver = new GoutteDriver();
$driver = new Selenium2Driver();

// Important object #2
$session = new Session($driver);

$session->start();
$session->visit('http://jurassicpark.wikia.com');

//echo "Status code: ". $session->getStatusCode() . "\n";
echo "Current URL: ". $session->getCurrentUrl() . "\n";

// Important object #3 DocumentElement
$page = $session->getPage();

echo "First 160 chars: ".substr($page->getText() , 0, 75) . "\n";

// Important object #4 NodeElement
$header = $page->find('css', '.WikiHeader .WikiNav h1');

echo "The wiki nav text is: ".$header->getText()."\n";

$subNav = $page->find('css', '.subnav-2');
$linkEl = $subNav->find('css', 'li a');

$linkEl = $page->findLink('Wiki Activity');

echo "The link href is: ". $linkEl->getAttribute('href') . "\n";

$linkEl->click();
echo "Page URL after click: ". $session->getCurrentUrl() . "\n";

$session->stop();