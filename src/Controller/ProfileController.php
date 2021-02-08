<?php


namespace Refiler\Controller;



use Refiler\Model\FileModel;
use Refiler\ORM\FileMapper;
use Refiler\ORM\UserMapper;
use Refiler\Util\FileHelper;
use Refiler\Util\PropertyBag;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Refiler\Controller\Contract\BaseController;

class ProfileController extends BaseController
{
    public function actIndex(Request $request, Response $response): Response {
        $users = $this->container->get(UserMapper::class);
        $id = $this->auth->getUserId();
        $user = $users->find($id);
        [$fiesTotal, $sizeTotal] = $this->getStats($user);
        $page = $this->renderer->render('profile.twig',[
            'title' => 'Profile',
            'totalCount' => $fiesTotal,
            'totalSize' => $sizeTotal,
            'user' => $user->toArray(),
        ]);
        $response->getBody()->write($page);
        return $response;
    }

    public function actUpdate(Request $request, Response $response): Response {
        $users = $this->container->get(UserMapper::class);
        /** @var PropertyBag $body */
        $body = $request->getAttribute('bag');
        $username = $body->getProperty('username');
        $fistName = $body->getProperty('first_name');
        $lastName = $body->getProperty('last_name');
        $id = $this->auth->getUserId();
        $user = $users->find($id);
        $user->username = $username;
        $user->first_name = $fistName;
        $user->last_name = $lastName;
        $user->save();
        return $response->withStatus(302)->withHeader('location','/profile');
    }

    private function getStats($user): array {
        $query = ['author' => (int)$user->id];
        $mapper = $this->container->get(FileMapper::class);
        $files = $mapper->findBy($query);
        $count = count($files);
        $sizeTotal = 0;
        $countSize = static function(FileModel $file) use (&$sizeTotal) : void  {
            $sizeTotal += $file->size;
        };
        array_walk($files, $countSize);
        return [$count, FileHelper::getAppropriateSizeFormat($sizeTotal)];
    }
}