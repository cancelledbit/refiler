<?php

use Refiler\Controller\AdminController;
use Refiler\Controller\ProfileController;
use Refiler\Controller\RemoveFileController;
use Refiler\Middleware\Request\AdminGuard;
use Refiler\Middleware\Request\AuthGuard;
use Refiler\Middleware\Request\PropertyBagRequestInjector;
use Slim\Factory\AppFactory;
use Refiler\Controller\HomeController;
use Refiler\Controller\UploadController;
use Refiler\Controller\DownloadController;
use Refiler\Controller\AuthContoller;
require __DIR__ . '/../vendor/autoload.php';


$builder = new DI\ContainerBuilder();
$builder->addDefinitions('..'.DIRECTORY_SEPARATOR.'config.php', '..'.DIRECTORY_SEPARATOR.'bootstrap.php');
$container = $builder->build();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->add(PropertyBagRequestInjector::class);
$errorHandler = $app->addErrorMiddleware(true,true,true);

$app->get('/',HomeController::class.':actIndex');
$app->get('/show/{id}',HomeController::class.':actShowSingle');
$app->get('/upload', UploadController::class.':actIndex');
$app->post('/upload', UploadController::class.':actUpload');
$app->get('/download/{file}', DownloadController::class.':actIndex');
$app->get('/download/{file}/preview', DownloadController::class.':actPreview');
$app->get('/login', AuthContoller::class.':actLoginIndex');
$app->get('/thankyou', AuthContoller::class.':actFinish');
$app->post('/login', AuthContoller::class.':actLogin');
$app->get('/register', AuthContoller::class.':actRegisterIndex');
$app->post('/register', AuthContoller::class.':actRegister');
$app->get('/logout', AuthContoller::class.':actLogOut');
$app->get('/remove/{file}', RemoveFileController::class.':actRemove')->add(AuthGuard::class);
$app->get('/profile', ProfileController::class.':actIndex')->add(AuthGuard::class);
$app->post('/profile/update', ProfileController::class.':actUpdate')->add(AuthGuard::class);
$app->get('/admin', AdminController::class.':actIndex')->add(AdminGuard::class);
$app->run();