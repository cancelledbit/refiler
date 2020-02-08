<?php


namespace Refiler\Controller;


use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Twig\Environment;

abstract class BaseController
{
    protected Environment $renderer;
    protected ContainerInterface $container;

    public function __construct(Environment $renderer, ContainerInterface $container)
    {
        $this->renderer = $renderer;
        $this->container = $container;
    }
}