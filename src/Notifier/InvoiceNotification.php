<?php


namespace App\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Mime\RawMessage;

class InvoiceNotification extends Notification implements ChatNotificationInterface, EmailNotificationInterface
{
    private $price;

    public function __construct(int $price, $subject, $channel = [])
    {
        parent::__construct($subject, $channel);
        $this->price = $price;
    }

    public function getChannels(Recipient $recipient) : array
    {
        if (
            $this->price > 10000
            && $recipient instanceof AdminRecipient
            && null !== $recipient->getPhone()
        ) {
            return ['sms'];
        }

        return ['email'];
    }

    public function asChatMessage(Recipient $recipient, string $transport = null): ?ChatMessage
    {
        // Add a custom emoji if the message is sent to Slack
        if ('slack' === $transport) {
            return (new ChatMessage('You\'re invoiced '.$this->price.' EUR.'))
                ->emoji('money');
        }

        // If you return null, the Notifier will create the ChatMessage
        // based on this notification as it would without this method.
        return null;
    }

    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {

        if ('email' === $transport) {
            return (new EmailMessage(new RawMessage('You\'re invoiced '.$this->price.' EUR. Click here to check.')));
        }

        // If you return null, the Notifier will create the ChatMessage
        // based on this notification as it would without this method.
        return null;
    }

}
