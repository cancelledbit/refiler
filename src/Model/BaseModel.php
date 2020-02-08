<?php
namespace Refiler\Model;

use MongoDB\BSON\ObjectId;

abstract class BaseModel
{
    protected \MongoDB\Collection $collection;
    public array $properties = [];

    public function __set(string $name, $value) {
        $this->properties[$name] = $value;
    }

    public function __get(string $name) {
        $this->properties[$name];
    }

    public function __construct(\Psr\Container\ContainerInterface $container) {
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
        return $this->getCollecton()->findOne($query);
    }

    public function findBy(array $query, ?array $order = null) {
        if ($order !== null) {
            $query['$orderBy'] = $order;
        }
        return $this->getCollecton()->find($query);
    }

    protected function getIdQuery(?string $id) {
        $id = new ObjectId($id);
        return [
            '_id' => $id,
        ];
    }

    public function save() {
        $id = $this->properties['_id'];
        if ($this->find($id) !== null) {
            $query = $this->getIdQuery($id);
            $this->getCollecton()->updateOne($query, $this->properties);
        } else {
            if (!$this->properties['_id'] instanceof ObjectId) {
                unset($this->properties['_id']);
            }
            $result = $this->getCollecton()->insertOne($this->properties);
            $this->properties['_id'] = $result->getInsertedId();
        }
        return $this;
    }

    public function generateId() {
        $this->properties['_id'] = new ObjectId();
        return $this->properties['_id'];
    }

    public function getId() {
        return $this->properties['_id'] instanceof ObjectId ? $this->properties['_id'] : $this->generateId();
    }

    public function getIdStr() {
        return (string) $this->getId();
    }

    private function getCollectionNameFromClass(string $name) {
        $name = explode('\\', $name);
        return array_pop($name);
    }
}