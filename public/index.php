<?php
use Slim\Factory\AppFactory;
use Refiler\Controller\HomeController;
use Refiler\Controller\UploadController;
use Refiler\Controller\DownloadController;
require __DIR__ . '/../vendor/autoload.php';


$builder = new DI\ContainerBuilder();
$builder->addDefinitions('..'.DIRECTORY_SEPARATOR.'config.php', '..'.DIRECTORY_SEPARATOR.'bootstrap.php');
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();
$errorHandler = $app->addErrorMiddleware(true,true,true);
$app->get('/',HomeController::class.':actIndex');
$app->get('/upload', UploadController::class.':actIndex');
$app->post('/upload', UploadController::class.':actUpload');
$app->get('/download/{file}', \Refiler\Controller\DownloadController::class.':actIndex');
$app->run();