<?php
namespace Refiler\Model\Contract;

use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use Refiler\Exception\File\FileNotFoundException;
use Refiler\ORM\Contract\MapperInterface;
use Refiler\ORM\Contract\MongoMapper;

abstract class BaseModel implements ModelInterface
{
    protected ContainerInterface $container;
    protected MapperInterface $mapper;

    protected bool $exists = false;
    protected array $properties = [
        '_id' => null,
    ];

    public function isExists()
    {
        return $this->exists;
    }

    public function getMapper() {
        if ($this->mapper === null) {
            throw new \Exception("Mapper is not defined");
        }
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

    public function __construct(array $properties = []) {
        $this->setProperties($properties);
    }

    public function setProperties(array $properties) {
        foreach ($this->getPropertiesKeys() as $key) {
            if (array_key_exists($key, $properties)) {
                $this->$key = $properties[$key];
                $this->setExisted();
            }
        }
        return $this;
    }
    public function generateBSONId() {
        $this->properties['_id'] = new ObjectId();
        return $this->properties['_id'];
    }

    public function getId() {
        if ($this->mapper instanceof MongoMapper) {
            return $this->properties['_id'] instanceof ObjectId ? $this->properties['_id'] : $this->generateBSONId();
        }
        return $this->properties['id'];
    }

    public function getIdStr() {
        return (string) $this->getId();
    }

    public function toArray() {
        $props = $this->properties;
        if (array_key_exists('id', $props)) {
            unset($props['id']);
        }
        return $props;
    }

    public function save() : void {
        $this->getMapper()->save($this);
        $this->setExisted();
    }

    public function remove() : bool {
        try {
            $this->getMapper()->remove($this);
        } catch (FileNotFoundException $e) {
            return false;
        }
        return true;
    }

    public function setExisted() : void {
        $this->exists = true;
    }

    public function setMapper(MapperInterface $mapper) : void {
        $this->mapper = $mapper;
    }
}