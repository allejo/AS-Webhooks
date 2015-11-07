<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once(__DIR__ . '/../src/AppKernel.php');

// Agenda Uploads Endpoints
$app->get('/agendauploads/{id}', 'allejo\DaPulser\Controller\WebController::twigAgendaRequestAction');
$app->post('/agendauploads', 'allejo\DaPulser\Controller\WebController::postAgendaRequestAction');

// Web Requests Endpoints
$app->get('/webrequest/{id}', 'allejo\DaPulser\Controller\WebController::twigWebRequestAction');
$app->post('/webrequest', 'allejo\DaPulser\Controller\WebController::postWebRequestAction');

// Marketing Requests Endpoints
$app->get('/genrequest/{id}', 'allejo\DaPulser\Controller\MarketingController::twigGeneralMarketingAction');
$app->post('/genrequest', 'allejo\DaPulser\Controller\MarketingController::postGeneralMarketingAction');

$app->run();
