<?php


namespace Refiler\Middleware\Request;


use Delight\Auth\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthGuard implements MiddlewareInterface
{
    private Auth $auth;
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(!$this->auth->isLoggedIn()) {
            $response = new Response();
            return $response->withStatus(302)->withHeader('Location','/login');
        }
        return $handler->handle($request);
    }
}