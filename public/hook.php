<?php

// Load composer
require __DIR__ . '/../vendor/autoload.php';

$bot_api_key = 'key';
$bot_username = 'RefilerBot';
$commands_paths = [
    __DIR__ . '/../src/Telegram/Commands/',
];
$builder = new DI\ContainerBuilder();
$builder->addDefinitions('..'.DIRECTORY_SEPARATOR.'config.php', '..'.DIRECTORY_SEPARATOR.'bootstrap.php');
$container = $builder->build();
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
    $telegram->addCommandsPaths($commands_paths);
    $mysql_credentials = [
        'host'     => '127.0.0.1',
        'port'     => 3306, // optional
        'user'     => 'tg',
        'password' => 'password',
        'database' => 'telegram',
    ];
    $conf['container'] = $container;
    $telegram->setCommandConfig('upload', $conf);
    $telegram->enableMySql($mysql_credentials);
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
     echo $e->getMessage();
}
