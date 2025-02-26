<?php

namespace App\MessageRealTime\MessageHandler;

use App\MessageRealTime\Message\MessageQueue;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessageQueueHandler implements MessageHandlerInterface
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function __invoke(MessageQueue $message)
    {
        dump('MessageQueueHandler');
        // Diffuser le message via WebSockets (Mercure)
        $update = new Update(
            "/messages",
            json_encode([
                'message' => $message->getContent(),
                'timestamp' => time(),
            ])
        );

        $this->hub->publish($update);
    }
}
