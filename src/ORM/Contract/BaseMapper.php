<?php


namespace Refiler\ORM\Contract;


use Psr\Container\ContainerInterface;
use Refiler\Model\Contract\BaseModel;
use Refiler\Model\Contract\ModelInterface;
use Refiler\Model\Factory\Contract\AbstractModelFactoryInterface;
use Refiler\Model\Factory\Contract\BaseModelFactory;

abstract class BaseMapper implements MapperInterface
{
    protected ContainerInterface $container;
    protected AbstractModelFactoryInterface $factory;
    protected string $model;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->setModel();
    }

    public function createModel() {
        $model = new $this->model();
        $model->setMapper($this);
        return $model;
    }
    private function setModel() : void {
        $name = explode('\\', static::class);
        $name = array_pop($name);
        $namePositionEnd = strpos($name, 'Mapper');
        $name = substr($name, 0, $namePositionEnd);
        $reflection = new \ReflectionClass(BaseModel::class);
        $namespace = $reflection->getNamespaceName();
        $namespace = str_replace('\Contract', '', $namespace);
        $class = $namespace.'\\'.$name.'Model';
        $this->model = $class;
    }
}