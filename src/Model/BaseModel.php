<?php
namespace Refiler\Model;

use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use Refiler\ORM\BaseMapper;

abstract class BaseModel
{
    protected ContainerInterface $container;
    protected BaseMapper $mapper;

    protected array $properties = [
        '_id' => null,
    ];

    public function getMapper() {
        return $this->mapper;
    }

    public function getPropertiesKeys() {
        return array_keys($this->properties);
    }

    public function __set(string $name, $value) {
        $this->properties[$name] = $value;
    }

    public function __get(string $name) {
        return $this->properties[$name];
    }

    public function __construct(\Psr\Container\ContainerInterface $container) {
        $this->container = $container;
        $this->setMapper();
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

    public function toArray() {
        return $this->properties;
    }

    public function save() {
        $this->getMapper()->save($this);
    }

    private function setMapper() {
        $name = explode('\\', static::class);
        $name = array_pop($name);
        $namePositionEnd = strpos($name, 'Model');
        $name = substr($name, 0, $namePositionEnd);
        $reflection = new \ReflectionClass(BaseMapper::class);
        $namespace = $reflection->getNamespaceName();
        $this->mapper = $this->container->get($namespace.'\\'.$name.'Mapper');
    }
}