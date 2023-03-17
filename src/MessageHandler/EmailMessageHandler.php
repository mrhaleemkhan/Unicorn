<?php

namespace App\MessageHandler;

use App\Message\EmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;


/**
 * Class EmailMessageHandler
 * @package App\MessageHandler
 */
class EmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;

    /**
     * EmailMessageHandler constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param EmailMessage $emailMessage
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function __invoke(EmailMessage $emailMessage)
    {
        $email = (new Email())
            ->from('noreply@unicorn.com')
            ->to($emailMessage->getTo())
            ->subject($emailMessage->getSubject())
            ->text($emailMessage->getBody());

        $this->mailer->send($email);
    }
}