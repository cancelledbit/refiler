<?php


namespace Longman\TelegramBot\Commands\UserCommands;

use Hybridauth\HttpClient\Guzzle;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\File;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Psr\Container\ContainerInterface;
use Refiler\Model\FileModel;
use Refiler\ORM\FileMapper;

class UploadCommand extends UserCommand
{
    private const DOWNLOAD_LINK = 'https://api.telegram.org';
    protected $name = 'upload';
    protected $description = 'Upload and save files';
    protected $usage = '/upload';
    protected $need_mysql = false;
    protected ?ContainerInterface $container = null;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat    = $message->getChat();
        $chat_id = $chat->getId();
        $user_id = $message->getFrom()->getId();
        /** @var ContainerInterface $container */
        $this->container = $this->getConfig('container');
        // Preparing Response
        $data = [
            'chat_id'      => $chat_id,
            'reply_markup' => Keyboard::remove(),
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            // Reply to message id is applied by default
            $data['reply_to_message_id'] = $message->getMessageId();
            // Force reply is applied by default to so can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        // Start conversation
        $conversation = new Conversation($user_id, $chat_id, $this->getName());
        $message_type = $message->getType();

        if (in_array($message_type, ['audio', 'document', 'photo', 'video', 'voice'], true)) {
            $doc = $message->{'get' . ucfirst($message_type)}();

            // For photos, get the best quality!
            ($message_type === 'photo') && $doc = end($doc);

            $file_id = $doc->getFileId();
            $file = Request::getFile(['file_id' => $file_id])->getResult();
            $model = $this->saveFileContents($file, $user_id);
            $data['text'] = "Public url for your file: {$model->getShowHref($this->container->get('sitename'))}";
            $conversation->notes['file_id'] = $file_id;
            $conversation->update();
            $conversation->stop();
        } else {
            $data['text'] = 'Please upload the file now';
        }

        return Request::sendMessage($data);
    }

    private function saveFileContents(File $file, int $userId): FileModel {
        $api = $this->telegram->getApiKey();
        $path = $file->getFilePath();
        $fileUrl = '/file/bot'.$api.'/'.$path;
        $file = static::prepareFileStruct($file, $userId);

        $mapper = $this->container->get(FileMapper::class);
        $model = $mapper->getFileModelFromTelegram($file);
        $client = new Guzzle(null, ['base_uri' => static::DOWNLOAD_LINK]);
        $fh = fopen('../storage/'.$model->_id, 'wb+');
        $response = $client->request($fileUrl, 'GET');
        $content = $response->getContents();
        fwrite($fh, $content);
        fclose($fh);
        $model->save();

        return $model;
    }

    private static function prepareFileStruct(File $file, int $userId) : array {
        $fileStruct = [
            'name' => null,
            'size' => null,
            'extension' => null,
            'author' => null,
        ];
        $uniqIdPart = substr($file->getFileId(),0, 4);
        $originalFilename = explode('/',$file->getFilePath())[1];
        $explodedPath = explode('.',$file->getFilePath());
        $extension = end($explodedPath);
        $fileStruct['name'] = $uniqIdPart.$originalFilename;
        $fileStruct['size'] = $file->getFileSize();
        $fileStruct['extension'] = $extension;
        $fileStruct['author'] = $userId;
        return $fileStruct;
    }
}
