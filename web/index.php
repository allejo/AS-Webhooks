<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once(__DIR__ . '/../src/AppKernel.php');

$app->get('/{hook}/{id}/send', 'allejo\DaPulser\Controller\MainController::sendRequestAction');
$app->get('/{hook}/{id}', 'allejo\DaPulser\Controller\MainController::getRequestAction');
$app->post('/{hook}', 'allejo\DaPulser\Controller\MainController::postRequestAction');

$app->run();
