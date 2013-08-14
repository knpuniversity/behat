<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Silex\Provider\UrlGeneratorServiceProvider;
use RaptorStore\Product;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use RaptorStore\User;

$app = new Silex\Application();
$app['debug'] = true;

/*
 * Services
 */
$app['schema_manager'] = $app->share(function($app) {
    return new \RaptorStore\SchemaManager($app['db'], $app['product_repository'], $app['user_repository']);
});

$app['product_repository'] = $app->share(function($app) {
    return new \RaptorStore\ProductRepository($app['db'], $app['user_repository']);
});
$app['user_repository'] = $app->share(function($app) {
    // create a dummy user to get the encoder
    $user = new User();

    return new \RaptorStore\UserRepository(
        $app['db'],
        $app['security.encoder_factory']->getEncoder($user)
    );
});

/*
 * Register the providers
 */
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../data/app.db',
    ),
));
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'anonymous' => true,
            'pattern' => '^/',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            // lazily load the user_repository
            'users' => $app->share(function () use ($app) {
                return $app['user_repository'];
            }),
            'logout' => array('logout_path' => '/admin/logout'),
        ),
    )
));
// access controls
$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
);

return $app;