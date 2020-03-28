<?php


namespace Refiler\ORM;


use Delight\Auth\Auth;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\Factory\FileFactory;
use Refiler\Model\FileModel;
use Refiler\ORM\Contract\MongoMapper;

class FileMapper extends MongoMapper
{
    public function __construct(ContainerInterface $container, Database $mongo)
    {
        parent::__construct($container, $mongo);
    }

    public function findByAuthor(int $author): array {
        $query = ['author' => $author];
        return $this->findBy($query);
    }

    public function getFileModelFromUploadedFile(UploadedFileInterface $file) : FileModel {
        /** @var FileModel $model */
        $model = $this->createModel();
        $extraInfo = pathinfo($file->getClientFilename());
        if (!array_key_exists('filename', $extraInfo)) {
            throw new \Exception("File must have name!");
        }
        $auth = $this->container->get(Auth::class);
        $model->author = $auth->getUserId();
        $model->_id = $model->getId();
        $model->name = $extraInfo['basename'];
        $model->extension = $extraInfo['extension'];
        $model->size = $file->getSize();
        $model->generateHref();
        return $model;
    }
}