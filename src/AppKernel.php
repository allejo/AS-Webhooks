<?php

use allejo\DaPulse\PulseBoard;

// Create the main application
$app = new Silex\Application();

// Register kernel extensions
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../config.yml'));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

// Register Twig extensions
$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    $twig->addExtension(new allejo\DaPulser\Twig\DateParserFilter($app));
    $twig->addExtension(new allejo\DaPulser\Twig\WufooUploadFilter($app));

    return $twig;
}));

// Setup our DaPulse library
PulseBoard::setApiKey($app['config']['dapulse_key']);

return $app;