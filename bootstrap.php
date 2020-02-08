<?php
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Psr\Container\ContainerInterface;

return [
    Environment::class => function (ContainerInterface $container) {
        $loader = new FilesystemLoader($container->get('twig.template'));
        return new Environment($loader);
    },
    'Database' => function (ContainerInterface $container) {
        $host = $container->get('db.host');
        $port = $container->get('db.port');
        $dbName = $container->get('db.name');
        $client = new \MongoDB\Client("mongodb://$host:$port");
        return $client->selectDatabase($dbName);
    },
    'Filesystem' => function (ContainerInterface $container) {
        $adapter = new LocalAdapter($container->get('storage.path'), true, 0750);
        return new Filesystem($adapter);
    }
];