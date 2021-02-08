<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{
    protected $name = 'start';

    protected $description = 'Print commands';

    protected $usage = '/start';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = "Wellcome to Refiler! \n Send /upload to upload file!";
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        return Request::sendMessage($data);
    }
}