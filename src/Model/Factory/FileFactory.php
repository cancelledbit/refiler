<?php


namespace Refiler\Model\Factory;


use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\BaseModel;
use Refiler\Model\FileModel;

class FileFactory extends AbstractModelFactory {


    public function getModel(array $properties = []): FileModel
    {
        /** @var FileModel $model */
        $model = new FileModel($this->container);
        foreach ($model->getPropertiesKeys() as $key) {
            if (array_key_exists($key, $properties)) {
                $model->$key = $properties[$key];
            }
        }
        return $model;
    }

    public function getModelFromUploadedFile(UploadedFileInterface $file) : FileModel {
        /** @var FileModel $model */
        $model = new FileModel($this->container);
        $extraInfo = pathinfo($file->getClientFilename());
        if (!array_key_exists('filename', $extraInfo)) {
            throw new \Exception("File must have name!");
        }
        $model->_id = $model->getId();
        $model->name = $extraInfo['basename'];
        $model->extension = $extraInfo['extension'];
        $model->size = $file->getSize();
        $model->generateHref();
        return $model;
    }
}