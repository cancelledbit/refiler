<?php


namespace Refiler\Controller;

use Illuminate\Support\Collection;
use Refiler\Controller\Contract\BaseController;
use Refiler\ORM\FileMapper;
use Refiler\Util\CollectionHelper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AdminController extends BaseController
{
    public function actIndex(Request $request, Response $response): Response {
        $mapper = $this->container->get(FileMapper::class);
        $files = new Collection($mapper->findBy([]));
        $block = $this->renderer->render('index.twig',['title' => 'All files','files' => $files->map(CollectionHelper::getFilePreparedForView())]);
        $response->getBody()->write($block);
        return $response;
    }
}