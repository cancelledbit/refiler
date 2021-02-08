<?php
namespace Refiler\Controller;

use Gaufrette\Filesystem;
use Refiler\Controller\Contract\BaseController;
use Refiler\Model\FileModel;
use Refiler\ORM\FileMapper;
use Refiler\Util\FileHelper;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

class DownloadController extends BaseController {
    public function actIndex(Request $request, Response $response): Response {
        $id = $request->getAttribute('file');
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

    public function actPreview(Request $request, Response $response): Response {
        $id = $request->getAttribute('file');
        $mongoFile = $this->container->get(FileMapper::class);
        /** @var FileModel $file */
        $file = $mongoFile->find($id);
        if ($file === null || !FileHelper::isImage($file)) {
            return $response->withStatus(404);
        }
        $extension = strtolower($file->extension);
        $extension = $extension === 'jpg' ? 'jpeg' : $extension;
        $fh = call_user_func_array('imagecreatefrom'.$extension, ['../storage/'.$id]);
        try {
            $fhResized = imagescale($fh, 300);
            $tmp = tmpfile();
            imagejpeg($fhResized, $tmp);
            return $response->withHeader('Content-Type', 'image/jpeg')->withBody(new Stream($tmp));
        } catch (\Exception $exception) {
            return $response->withStatus(404);
        }
    }
}