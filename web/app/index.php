<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Routing\RouteCollection;
use Silex\Application;


$config = Yaml::parse(__DIR__ . '/../../config/parameters.yml');

#print_r($config); exit;

$app          = new Silex\Application();
$app['debug'] = true;

$app['routes'] = $app->extend(
    'routes',
    function (RouteCollection $routes, Application $app) {
        $loader     = new YamlFileLoader(new FileLocator(__DIR__ . '/config'));
        $collection = $loader->load('routes.yml');
        $routes->addCollection($collection);

        return $routes;
    }
);

$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    array(
        'db.options' => array(

            'driver'    => $config['parameters']['database_driver'],
            'host'      => $config['parameters']['database_host'],
            'dbname'    => $config['parameters']['database_name'],
            'user'      => $config['parameters']['database_user'],
            'password'  => $config['parameters']['database_password'],

        ),
    )
);

$app->register(new \Geocoder\Provider\GeocoderServiceProvider());

// we configure our provider here
$app['geocoder.provider'] = $app->share(function () use ($app) {
    return new \Geocoder\Provider\GoogleMapsProvider($app['geocoder.adapter']);
});

$app->run();