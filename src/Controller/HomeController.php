<?php


namespace Refiler\Controller;

use Refiler\ORM\FileMapper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class HomeController extends BaseController
{
    public function actIndex(Request $request, Response $response)
    {
        $mapper = $this->container->get(FileMapper::class);
        $files = $mapper->findBy(['name' => [
            '$exists' => 'true',
        ]]);
        $allFiles = [];
        foreach ($files as $file) {
            $allFiles[] = [
                'name' => $file->name,
                'size' => $file->size,
                'href' => $file->href,
                '_id' => $file->_id,
            ];
        }
        $block = $this->renderer->render('index.twig',['title' => 'Refiler','files' => $allFiles]);
        $response->getBody()->write($block);
        return $response;
    }
}