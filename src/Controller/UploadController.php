<?php


namespace Refiler\Controller;


use Gaufrette\Filesystem;
use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\Factory\FileFactory;
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
            $fileFactory = $this->container->get(FileFactory::class);
            $newFile = $fileFactory->getModelFromUploadedFile($file);
            $id = $newFile->getIdStr();
            /** @var Filesystem $fs */
            $fs = $this->container->get('Filesystem');
            $fs->write($id, $file->getStream()->getContents());
            $newFile->save();
        }
        return $response->withStatus(302)->withHeader('location', '/');
    }

}