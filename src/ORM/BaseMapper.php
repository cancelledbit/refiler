<?php


namespace Refiler\ORM;


use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use Refiler\Model\BaseModel;
use Refiler\Model\Factory\AbstractModelFactory;
use Refiler\Model\Factory\AbstractModelFactoryInterface;
use Refiler\Model\Factory\FileFactory;

class BaseMapper
{
    protected \MongoDB\Collection $collection;
    protected ContainerInterface $container;
    protected AbstractModelFactory $factory;

    public function __construct(ContainerInterface $container, AbstractModelFactoryInterface $factory) {
        $this->container = $container;
        $this->factory = $this->container->get(get_class($factory));
        /** @var \MongoDB\Database $mongo */
        $mongo = $container->get('Database');
        $collectionName = $this->getCollectionNameFromClass(static::class);
        $this->collection = $mongo->selectCollection($collectionName);
    }

    public function getCollecton() {
        return $this->collection;
    }

    public function find(?string $id) {
        $query = $this->getIdQuery($id);
        $mongoItem = $this->getCollecton()->findOne($query);
        if ($mongoItem !== null) {
            return $this->factory->getModel((array)$mongoItem);
        }
        return null;
    }

    public function findBy(array $query, ?array $order = null) {
        if ($order !== null) {
            $query['$orderBy'] = $order;
        }
        $cursor = $this->getCollecton()->find($query);

        $result = [];
        /** @var FileFactory $factory */
        foreach ($cursor as $mongoItem) {
            $result[] = $this->factory->getModel((array)$mongoItem);
        }
        return $result;
    }

    protected function getIdQuery(?string $id) {
        $id = new ObjectId($id);
        return [
            '_id' => $id,
        ];
    }

    public function save(BaseModel $model) {
        $id = $model->getIdStr();
        if ($this->find($id) !== null) {
            $query = $this->getIdQuery($id);
            $this->getCollecton()->updateOne($query, $model->toArray());
        } else {
            if (!$this->properties['_id'] instanceof ObjectId) {
                unset($this->properties['_id']);
            }
            $result = $this->getCollecton()->insertOne($model->toArray());
            $model->_id = $result->getInsertedId();
        }
        return $model;
    }

    private function getCollectionNameFromClass(string $name) {
        $name = explode('\\', $name);
        $name = array_pop($name);
        $namePositionEnd = strpos($name, 'Mapper');
        return substr($name, 0, $namePositionEnd).'Model';
    }
}