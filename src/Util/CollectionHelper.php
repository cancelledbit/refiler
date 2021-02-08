<?php


namespace Refiler\Util;


use Refiler\Model\FileModel;

class CollectionHelper
{
    public static function getFilePreparedForView(): \Closure {
        return static function(FileModel $file): array {
            return [
                'name' => strlen($file->name) > 15 ? substr($file->name, 0, 15).'...' : $file->name,
                'size' => FileHelper::getAppropriateSizeFormat($file->size),
                'href' => $file->href,
                '_id' => $file->_id,
                'selector' => substr($file->getIdStr(), 0, 3),
                'preview' => $file->getPreviewUrl(),
            ];
        };
    }
}