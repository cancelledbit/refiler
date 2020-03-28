<?php


namespace Refiler\Controller;


use Refiler\ORM\Contract\BaseMapper;
use Refiler\ORM\FileMapper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;
use Refiler\Controller\Contract\BaseController;

class RemoveFileController extends BaseController {

    public function actRemove(Request $request, Response $response) {
        $fileId = $request->getAttribute('file');
        $mongoFile = $this->container->get(FileMapper::class);
        if ($fileId === 'all') {
            $files = $mongoFile->findByAuthor($this->auth->getUserId());
            foreach ($files as $file) {
                if($file->remove()) {
                    $this->filesystem->delete($file->getIdStr());
                }
            }
            return $response->withStatus(302)->withHeader('Location', '/');
        }  else {
           return $this->removeFile($fileId, $response, $mongoFile);
        }
    }

    protected function removeFile(string $id, Response $response, BaseMapper $mapper): Response {
        $file = $mapper->find($id);
        if ($file->author === (int) $this->auth->getUserId() && $file->remove()) {
            $this->filesystem->delete($id);
            return $response->withStatus(302)->withHeader('Location', '/');
        } else {
            return $response->withStatus(404);
        }
    }

}
