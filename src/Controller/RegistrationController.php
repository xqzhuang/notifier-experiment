<?php


namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\AdminRecipient;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/**
 * Class RegistrationController
 * @package App\Controller
 * @Route("/registration")
 */
class RegistrationController extends AbstractController
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/confirmation/{userId}")
     * @param NotifierInterface $notifier
     * @param $userId
     */
    public function confirm(MailerInterface $mailer, $userId = '10000')
    {
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('foo@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Thank you for signing up!')
            ->text('Sign up succeed.')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
            ]);

        $mailer->send($email);

        return new Response(
            '<html><body>Sent an email</body></html>'
        );
    }

    /**
     *
     * @Route("/verification/{userId}")
     * @param NotifierInterface $notifier
     * @param $userId
     */
    public function verify(NotifierInterface $notifier)
    {
        $user = $this->security->getUser();

        $notification = (new Notification('My Shop', ['sms']))
            ->content('Your verification code is: 9384394.');

        // The receiver of the Notification
        $recipient = new AdminRecipient(
            $user->getEmail(),
            $user->getPhonenumber()
        );

        // Send the notification to the recipient
        $notifier->send($notification, $recipient);

        return new Response(
            '<html><body>Verification code has been sent!</body></html>'
        );
    }

    /**
     *
     *@Route("/notify-to-admin")
     * @param NotifierInterface $notifier
     * @return Response
     */
    public function notifyToAdmin(NotifierInterface $notifier)
    {
        $notification = (new Notification('New User'))
            ->content('A new user signed up. Go and approve it.')
            ->importance(Notification::IMPORTANCE_URGENT);

        // The receiver of the Notification
        $recipient = new AdminRecipient(
            'admin@example.com',
            '1234456789'
        );

        // Send the notification to the recipient
        $notifier->send($notification, $recipient);

        return new Response(
            '<html><body>Notification has been sent!</body></html>'
        );
    }
}
