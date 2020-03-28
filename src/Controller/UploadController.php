<?php


namespace Refiler\Controller;


use Gaufrette\Filesystem;
use Refiler\Controller\Contract\BaseController;
use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\Factory\FileFactory;
use Refiler\ORM\FileMapper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UploadController extends BaseController
{
    public function actIndex(Request $request, Response $response) {
        $block = $this->renderer->load('upload.twig')->render(['title' => 'Refiler']);
        $response->getBody()->write($block);
        return $response;
    }

    public function actUpload(Request $request, Response $response) {
        $files = $request->getUploadedFiles();
        /** @var UploadedFileInterface $file */
        foreach ($files as $file) {
            /** @var FileMapper $fileMapper */
            $fileMapper = $this->container->get(FileMapper::class);
            try {
                $newFile = $fileMapper->getFileModelFromUploadedFile($file);
            } catch (\Exception $e) {
                die ('You did not provide filename!');
            }
            $id = $newFile->getIdStr();
            $this->filesystem->write($id, $file->getStream()->getContents());
            $newFile->save();
        }
        $json = json_encode(['id' => 'all']);
        if(!$this->auth->isLoggedIn()) {
            $json = json_encode(['id' => $id]);
        }
        $response->getBody()->write($json);
        return $response;
    }

}