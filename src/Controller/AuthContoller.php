<?php


namespace Refiler\Controller;


use Delight\Auth\AttemptCancelledException;
use Delight\Auth\Auth;
use Delight\Auth\AuthError;
use Delight\Auth\EmailNotVerifiedException;
use Psr\Container\ContainerInterface;
use Refiler\Util\PropertyBag;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Twig\Environment;

class AuthContoller extends BaseController
{
    protected Auth $auth;
    public function __construct(Environment $renderer, ContainerInterface $container)
    {
        parent::__construct($renderer, $container);
        $this->auth = $this->container->get(Auth::class);
    }

    public function actLoginIndex(Request $request, Response $response) {
        if ($this->auth->isLoggedIn()){
            return $response->withStatus(302)->withHeader('location', '/');
        }
        $page = $this->renderer->render('login.twig',['title' => 'login']);
        $response->getBody()->write($page);
        return $response;
    }

    public function actLogin(Request $request, Response $response) {
        if ($this->auth->isLoggedIn()){
            return $response->withStatus(302)->withHeader('location', '/');
        }
        $body = $request->getAttribute('bag');
        $email = $body->getProperty('email');
        $password = $body->getProperty('password');
        try {
            $this->auth->login($email, $password);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        } catch (AttemptCancelledException $e) {
            die($e->getMessage());
        } catch (AuthError $e) {
            die($e->getMessage());
        } catch (EmailNotVerifiedException $e) {
            die($e->getMessage());
        }
        return $response->withStatus(302)->withHeader('location','/');;
    }

    public function actRegisterIndex(Request $request, Response $response) {
        if ($this->auth->isLoggedIn()){
            return $response->withStatus(302)->withHeader('location', '/');
        }
        $page = $this->renderer->render('register.twig',['title' => 'Register']);
        $response->getBody()->write($page);
        return $response;
    }

    public function actRegister(Request $request, Response $response) {
        /** @var PropertyBag $body */
        $body = $request->getAttribute('bag');
        //$body->get = $request->getAttribute('last_name');
        $email = $body->getProperty('email');
        $password = $body->getProperty('password');
        try {
            $this->auth->register($email, $password);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('User already exists');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
        return $response->withStatus(302)->withHeader('location','/');
    }

    public function actLogOut(Request $request, Response $response) {
        try {
            $this->auth->logOutEverywhere();
            $this->auth->destroySession();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $response->withStatus(302)->withHeader('Location','/');
    }

}