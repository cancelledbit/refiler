<?php

namespace Refiler\ORM\Contract;

use Refiler\Model\Contract\BaseModel;

interface MapperInterface
{
    public function find(?string $id);

    public function findBy(array $query, ?array $order = null);

    public function save(BaseModel $model);

    public function remove(BaseModel $model);
}