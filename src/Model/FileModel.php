<?php
namespace Refiler\Model;
use \Refiler\Model\Contract\BaseModel;
use Refiler\Util\FileHelper;

/**
 * Class FileModel
 * @package Refiler\Model
 * @property string name
 * @property string author
 * @property string extension
 * @property float size
 * @property string href
 */
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

    public function generateHref(): FileModel {
        $this->href = '/download/'.$this->getIdStr();
        return $this;
    }

    public function getShowHref(string $site): string {
        return $site.'/show/'.$this->getIdStr();
    }

    public function getFullName(): string {
        return $this->name.'.'.$this->extension;
    }

    public function getPreviewUrl(): string {
        if (!FileHelper::isImage($this)) {
            return '';
        }
        return $this->href . '/preview';
    }

}