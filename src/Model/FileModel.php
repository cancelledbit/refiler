<?php
namespace Refiler\Model;
use \Refiler\Model\Contract\BaseModel;

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