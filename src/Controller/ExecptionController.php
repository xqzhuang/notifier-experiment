<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;

class ExecptionController extends AbstractController
{
    /**
     * @Route("/notify/exception")
     * @param NotifierInterface $notifier
     */
    public function notifyExceptionAction(NotifierInterface $notifier)
    {
        $notification = (new Notification('Exception'))
            ->content('System down!')
            ->importance(Notification::IMPORTANCE_URGENT); // Uses channels from config.

        $recipient = new AdminRecipient('xz@test.pl', '34874899');
        $notifier->send($notification, $recipient, $recipient);

        return new Response(
            '<html><body>Sent the exception.</body></html>'
        );
    }
}
