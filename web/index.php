<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once(__DIR__ . '/../src/AppKernel.php');

$app->post('/webrequest', 'allejo\DaPulser\Controller\WebController::webRequestAction');
$app->post('/genrequest', 'allejo\DaPulser\Controller\MarketingController::generalMarketingAction');
$app->run();
