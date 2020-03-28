<?php


namespace Refiler\ORM;


use Delight\Db\PdoDatabase;
use Psr\Container\ContainerInterface;
use Refiler\Model\Factory\AbstractModelFactoryInterface;
use Refiler\Model\Factory\UserFactory;
use Refiler\ORM\Contract\SQLMapper;

class UserMapper extends SQLMapper
{
    public function __construct(ContainerInterface $container, PdoDatabase $database)
    {
        parent::__construct($container, $database);
    }
}