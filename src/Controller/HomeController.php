<?php


namespace Refiler\Controller;

use Delight\Auth\Auth;
use MongoDB\BSON\ObjectId;
use Refiler\ORM\FileMapper;
use Refiler\Util\FileHelper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Refiler\Controller\Contract\BaseController;

class HomeController extends BaseController
{
    public function actIndex(Request $request, Response $response)
    {
        $mapper = $this->container->get(FileMapper::class);
        $auth = $this->container->get(Auth::class);
        $queryTemplate = [
            'name' => [
                '$exists' => 'true',
            ],
        ];
        $allFiles = [];
        if ($auth->getUserId() !== null) {
            $queryTemplate['author'] = $auth->getUserId();
            $files = $mapper->findBy($queryTemplate);
            foreach ($files as $file) {
                $allFiles[] = [
                    'name' => $file->name,
                    'size' => FileHelper::getAppropriateSizeFormat($file->size),
                    'href' => $file->href,
                    '_id' => $file->_id,
                ];
            }
        }
        $block = $this->renderer->render('index.twig',['title' => 'Refiler','files' => $allFiles]);
        $response->getBody()->write($block);
        return $response;
    }

    public function actShowSingle(Request $request, Response $response) {
        $mapper = $this->container->get(FileMapper::class);
        $id = $request->getAttribute('id');
        $queryTemplate['_id'] = new ObjectId($id);
        $file = $mapper->find($id);
        $allFiles[] = [
            'name' => $file->name,
            'size' => FileHelper::getAppropriateSizeFormat($file->size),
            'href' => $file->href,
            '_id' => $file->_id,
        ];
        $block = $this->renderer->render('index.twig', ['title' => 'Refiler', 'files' => $allFiles]);
        $response->getBody()->write($block);
        return $response;
    }
}