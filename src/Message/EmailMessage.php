<?php

namespace App\Message;


/**
 * Class EmailMessage
 * @package App\Message
 */
class EmailMessage
{

    private $to;

    private $subject;

    private $body;

    /**
     * EmailMessage constructor.
     * @param string $to
     * @param int $counter
     */
    public function __construct(string $to, int $counter)
    {
        $this->to = $to;
        $this->subject = "Congratulations on your unicorn purchase!";
        $this->body = sprintf('Congratulations on your unicorn purchase! %d posts were made for your newly bought unicorn.', $counter);
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}