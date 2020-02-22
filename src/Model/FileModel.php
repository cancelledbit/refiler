<?php
namespace Refiler\Model;
use Delight\Auth\Auth;
use Psr\Http\Message\UploadedFileInterface;
use Refiler\Model\BaseModel;

class FileModel extends BaseModel
{
    protected array $properties = [
        '_id' => null,
        'name' => null,
        'author' => null,
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

}