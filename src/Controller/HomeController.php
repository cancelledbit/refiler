<?php


namespace Refiler\Controller;

use Delight\Auth\Auth;
use Illuminate\Support\Collection;
use MongoDB\BSON\ObjectId;
use Refiler\ORM\FileMapper;
use Refiler\Util\CollectionHelper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Refiler\Controller\Contract\BaseController;

class HomeController extends BaseController
{
    public function actIndex(Request $request, Response $response): Response
    {
        $mapper = $this->container->get(FileMapper::class);
        $auth = $this->container->get(Auth::class);
        $queryTemplate = [
            'name' => [
                '$exists' => 'true',
            ],
        ];
        $files = new Collection([]);
        if ($auth->getUserId() !== null) {
            $queryTemplate['author'] = $auth->getUserId();
            $files = new Collection($mapper->findBy($queryTemplate));
        }
        $block = $this->renderer->render('index.twig',['title' => 'Refiler','files' => $files->map(CollectionHelper::getFilePreparedForView())]);
        $response->getBody()->write($block);
        return $response;
    }

    public function actShowSingle(Request $request, Response $response): Response {
        $mapper = $this->container->get(FileMapper::class);
        $id = $request->getAttribute('id');
        $queryTemplate['_id'] = new ObjectId($id);
        $file = $mapper->find($id);
        $block = $this->renderer->render('index.twig', ['title' => 'Refiler', 'files' => [CollectionHelper::getFilePreparedForView()($file)]]);
        $response->getBody()->write($block);
        return $response;
    }
}