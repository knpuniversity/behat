<?php
/** @var $app \Silex\Application */

use Symfony\Component\HttpFoundation\Request;

/**
 * Controllers and routes
 */

$app->get('/', function(\Silex\Application $app) {
    return $app['twig']->render('homepage.twig');
})->bind('homepage');

$app->get('/admin', function(\Silex\Application $app) {
    return $app['twig']->render('admin.twig');
})->bind('admin');

$app->get('/products', function(\Silex\Application $app) {
    return $app['twig']->render('products.twig', array(
        //'products' => $app['product_repository']->findAllPublished()
        'products' => $app['product_repository']->findAll()
    ));
})
    ->bind('product_list');

$app->get('/products/new', function(\Silex\Application $app) {
    // slows down the response, to challenge our Behat test! Silex is too fast otherwise!
    sleep(1);

    return $app['twig']->render('_product_new.twig');
})
    ->bind('product_new');

$app->post('/products/new', function(\Silex\Application $app, Request $request) {
    // really quick (but dangerous) way to load my data into a Product

    /** @var $product Product */
    $product = $app['product_repository']->arrayToObject($request->request->all());
    $app['product_repository']->insert($product);

    $request->getSession()->getFlashbag()->add('success', 'Product created FTW!');

    return $app->redirect($app['url_generator']->generate('product_list'));
})
    ->bind('product_new_handle');

// Login!
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

/**
 * Internal routes for resetting things
 */
$app->get('/_db/rebuild.{_format}', function(\Silex\Application $app, Request $request) {
    $app['schema_manager']->rebuildSchema();

    $withFixtures = !$request->query->has('fixtures') || $request->query->get('fixtures');

    if ($withFixtures) {
        $app['schema_manager']->loadFixtures();
    }

    return json_encode(array(
        'success' => true
    ));
})
    ->bind('db_rebuild')
    ->assert('_format', 'json')
;