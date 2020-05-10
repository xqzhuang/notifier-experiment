<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Notifier\InvoiceNotification;
use Symfony\Component\Notifier\Recipient\Recipient;

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

    /**
     * @Route("/send/email")
     */
    public function sendMailAction(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('admin@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Test Symfony Mailer!')
            ->text('A test to send email!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return new Response(
            '<html><body>Sent an email</body></html>'
        );
    }

    /**
     * Use customized notification.
     * @Route("/invoice/create")
     */
    public function createInvoiceAction(NotifierInterface $notifier)
    {
        $price = 20000;

        $notification = (new InvoiceNotification($price, 'New Invoice', []))
            ->content(sprintf('You got a new invoice for %s EUR.', $price))
            ->importance(InvoiceNotification::IMPORTANCE_HIGH);

        $recipient = new AdminRecipient('xz@test.pl', '34874899');
        $recipient1 = new AdminRecipient('bubu@test.pl', '34874598');

        $notifier->send($notification, $recipient, $recipient1);

        return new Response(
            '<html><body>Sent a notification.</body></html>'
        );
    }
}
