<?php

require_once __DIR__ . '/vendor/autoload.php';

use allejo\DaPulse\PulseProject;

// The app kernel
$app = new Silex\Application();

// Register kernel extensions
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/config.yml'));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

// Setup our DaPulse library
PulseProject::setApiKey($app['config']['dapulse_key']);

// Webhooks
$app->post('/hooks/webrequest', function() use($app) {
    $fields = array("EntryId", "DateCreated", "Field1", "Field2", "Field3", "Field4", "Field113", "Field5", "Field10",
                    "Field7", "Field7-url", "Field8", "Field8-url");
    $twigVars = array();

    foreach ($fields as $field)
    {
        $twigVars[$field] = $app['request']->get($field);
    }

	$content = $app['twig']->render('webrequest.html.twig', $twigVars);

    $webProject = new PulseProject(3457985);
    $webProject->addNote("Web Request Details", $content);
}); 

$app->run();
