<?php


namespace Refiler\ORM;


use Psr\Container\ContainerInterface;
use Refiler\Model\Factory\FileFactory;

class FileMapper extends BaseMapper
{
    public function __construct(ContainerInterface $container, FileFactory $factory)
    {
        parent::__construct($container, $factory);
    }
}