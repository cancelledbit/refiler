<?php


namespace Refiler\Model\Factory;


use MongoDB\Model\BSONDocument;
use Psr\Container\ContainerInterface;
use Refiler\Model\BaseModel;

abstract class AbstractModelFactory implements AbstractModelFactoryInterface
{
    protected $container;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    abstract public function getModel(array $properties): BaseModel;
}