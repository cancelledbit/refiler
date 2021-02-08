<?php


namespace Refiler\Middleware\Request;


use Delight\Auth\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AdminGuard extends AuthGuard
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->auth->hasRole(Role::ADMIN)) {
            $response = new Response();
            return $response->withStatus(302)->withHeader('Location','/login');
        }
        return $handler->handle($request);
    }
}