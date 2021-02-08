<?php

// Load composer
use Longman\TelegramBot\Telegram;

require __DIR__ . '/../vendor/autoload.php';

$bot_api_key = 'key';
$bot_username = 'RefilerBot';
$hook_url = 'https://refiler.ru/hook.php';

try {
    // Create Telegram API object
    $telegram = new Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    //$result = $telegram->deleteWebhook();
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {

     echo $e->getMessage();
}
