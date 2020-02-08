<?php
namespace Refiler\Model;
use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\BaseModel;

class FileModel extends BaseModel
{
    public array $properties = [
        '_id' => null,
        'name' => null,
        'extension' => null,
        'size' => 0,
        'href' => null,
    ];

    public function generateHref() {
        $this->href = '/download/'.$this->getIdStr();
        return $this;
    }
    public function getFullName() {
        return $this->name.'.'.$this->extension;
    }
    public function fillFromUploadedFile(UploadedFileInterface $file) {
        $extraInfo = pathinfo($file->getClientFilename());
        if (!array_key_exists('filename', $extraInfo)) {
            throw new \Exception("File must have name!");
        }
        $this->_id = $this->getId();
        $this->name = $extraInfo['basename'];
        $this->extension = $extraInfo['extension'];
        $this->size = $file->getSize();
        $this->generateHref();
        return $this;
    }
}