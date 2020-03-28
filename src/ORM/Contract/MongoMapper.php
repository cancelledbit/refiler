<?php


namespace Refiler\ORM\Contract;


use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Refiler\Exception\File\FileNotFoundException;
use Refiler\Model\Contract\BaseModel;
use Refiler\Model\Contract\ModelInterface;
use Refiler\Model\Factory\Contract\AbstractModelFactoryInterface;
use Refiler\Model\Factory\FileFactory;

abstract class MongoMapper extends BaseMapper implements MapperInterface
{
    protected \MongoDB\Collection $collection;

    public function __construct(ContainerInterface $container, Database $mongo) {
        parent::__construct($container);
        $collectionName = $this->getCollectionNameFromClass(static::class);
        $this->collection = $mongo->selectCollection($collectionName);
    }

    public function getCollecton(): Collection {
        return $this->collection;
    }

    public function find(?string $id): ModelInterface {
        $query = $this->getIdQuery($id);
        $mongoItem = $this->getCollecton()->findOne($query);
        $model = new $this->model((array)$mongoItem);
        $model->setMapper($this);
        if ($mongoItem !== null) {
            $model->setExisted();
        }
        return $model;
    }

    public function findBy(array $query, ?array $order = null): array {
        if ($order !== null) {
            $query['$orderBy'] = $order;
        }
        $cursor = $this->getCollecton()->find($query);

        $result = [];
        foreach ($cursor as $mongoItem) {
            $model = new $this->model((array)$mongoItem);
            $model->setMapper($this);
            $model->setExisted();
            $result[] = $model;

        }
        return $result;
    }

    private function getIdQuery(?string $id) : array {
        $id = new ObjectId($id);
        return [
            '_id' => $id,
        ];
    }

    public function save(BaseModel $model): ModelInterface {
        $id = $model->getIdStr();
        if ($this->find($id)->isExists()) {
            $query = $this->getIdQuery($id);
            $this->getCollecton()->updateOne($query, $model->toArray());
        } else {
            if (!$model->_id instanceof ObjectId) {
               $model->getIdStr();
            }
            $result = $this->getCollecton()->insertOne($model->toArray());
            $model->_id = $result->getInsertedId();
        }
        return $model;
    }

    public function remove(BaseModel $model) : bool {
        $id = $model->getIdStr();
        if ($this->find($id) !== null) {
            $query = $this->getIdQuery($id);
            $this->getCollecton()->deleteOne($query);
        } else {
            throw new FileNotFoundException('Requested for remove not found');
        }
        return true;
    }

    protected function getCollectionNameFromClass(string $name): string {
        $name = explode('\\', $name);
        $name = array_pop($name);
        $namePositionEnd = strpos($name, 'Mapper');
        return substr($name, 0, $namePositionEnd).'Model';
    }
}