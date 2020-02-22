<?php
namespace Refiler\Middleware\Request;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Refiler\Util\PropertyBag;

class PropertyBagRequestInjector implements MiddlewareInterface
{

    private ServerRequestInterface $request;
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = $request;
        $this->addPropertyBag();
        return $handler->handle($this->request);
    }

    private function addPropertyBag() {
        $properties = $this->request->getParsedBody();
        $bag = new PropertyBag($properties);
        $this->request = $this->request->withAttribute('bag', $bag);
    }
}