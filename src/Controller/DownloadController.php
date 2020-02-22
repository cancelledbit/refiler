<?php
namespace Refiler\Controller;

use Gaufrette\Filesystem;
use Refiler\Controller\BaseController;
use Refiler\Model\FileModel;
use Refiler\ORM\FileMapper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

class DownloadController extends BaseController {
    public function actIndex(Request $request, Response $response) {
        $id = $request->getAttribute('file');
        $fs = $this->container->get('Filesystem');
        $mongoFile = $this->container->get(FileMapper::class);
        $file = $mongoFile->find($id);
        if ($file === null) {
            return $response->withStatus(404);
        }
        $fh = fopen('../storage/'.$id, 'rb');
        $fileStream = new Stream($fh);
        return $response->withHeader('Content-Type', 'application/force-download')
            ->withHeader('Content-Type', 'application/'.$file->extension)
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Disposition', 'attachment; filename="' . basename($file->name) . '"')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Pragma', 'public')
            ->withBody($fileStream);
    }
}