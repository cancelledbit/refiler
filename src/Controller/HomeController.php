<?php


namespace Refiler\Controller;

use Refiler\Model\FileModel;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class HomeController extends BaseController
{
    public function actIndex(Request $request, Response $response)
    {
        $mapper = $this->container->get(FileModel::class);
        $files = $mapper->findBy(['name' => [
            '$exists' => 'true',
        ]]);
        $allFiles = [];
        foreach ($files as $file) {
            $allFiles[] = $file;
        }
        $block = $this->renderer->load('index.twig')->render(['title' => 'Refiler','files' => $allFiles]);
        $response->getBody()->write($block);
        return $response;
    }
}