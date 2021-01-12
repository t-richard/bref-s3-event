<?php declare(strict_types=1);

use Bref\Context\Context;
use Bref\Event\S3\S3Event;
use Bref\Event\S3\S3Handler;
use Symfony\Component\Notifier\Bridge\OvhCloud\OvhCloudTransport;
use Symfony\Component\Notifier\Bridge\Slack\SlackTransport;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\SmsMessage;

require __DIR__ . '/vendor/autoload.php';

class Handler extends S3Handler
{
    public function handleS3(S3Event $event, Context $context): void
    {
        $notifier = new OvhCloudTransport(
            $_SERVER['OVH_KEY'],
            $_SERVER['OVH_SECRET'],
            $_SERVER['CONSUMER_KEY'],
            $_SERVER['SERVICE_NAME'],
        );

        $slack = new SlackTransport($_SERVER['SLACK_TOKEN'], $_SERVER['SLACK_CHANNEL']);

        $slack->send(new ChatMessage('Nouvelle image sur le S3 ! '.$event->getRecords()[0]->getObject()->getKey()));

        $notifier->send(new SmsMessage($_SERVER['PHONE_NUMBER'], 'Nouvelle dick-pic sur le S3: ' . $event->getRecords()[0]->getObject()->getKey()));
    }
}

return new Handler();