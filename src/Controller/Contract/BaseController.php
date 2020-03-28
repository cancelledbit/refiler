<?php


namespace Refiler\Controller\Contract;


use Delight\Auth\Auth;
use Gaufrette\Filesystem;
use Psr\Container\ContainerInterface;
use Refiler\Middleware\Request\AuthGuard;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Twig\Environment;

abstract class BaseController
{
    protected Environment $renderer;
    protected ContainerInterface $container;
    protected Auth $auth;
    protected Filesystem $filesystem;

    public function __construct(Environment $renderer, ContainerInterface $container, Auth $auth, Filesystem $filesystem)
    {
        $this->auth = $auth;
        $this->filesystem = $filesystem;
        $this->renderer = $renderer;
        $this->container = $container;
    }
}