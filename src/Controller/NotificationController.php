<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class NotificationController extends AbstractController
{
    /**
     * @Route("/send/sms")
     */
    public function sendSmsAction(TexterInterface $texter)
    {
        $sms = new SmsMessage(
        // the phone number to send the SMS message to
            '+3847398478',
            // the message
            'A new login was detected!'
        );

        $texter->send($sms);

        return new Response(
            '<html><body>Sent SMS</body></html>'
        );
    }

    /**
     * @Route("/send/message")
     */
    public function sentMessageAction(ChatterInterface $chatter)
    {
        $message = (new ChatMessage('You got a new invoice for 15 EUR.'))
            // if not set explicitly, the message is send to the
            // default transport (the first one configured)
            ->transport('slack');

        $chatter->send($message);

        return new Response(
            '<html><body>Sent message to slack</body></html>'
        );
    }
}
