<?php

/** @var $app \Silex\Application */
$app = require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/../app/controllers.php';

$app->run();
