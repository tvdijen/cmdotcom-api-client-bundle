<?php

namespace tvdijen\CMDotCom\ApiClient\Messaging;

use tvdijen\CMDotCom\ApiClient\Exception\DomainException;
use tvdijen\CMDotCom\ApiClient\Exception\InvalidArgumentException;

use function is_string;
use function preg_match;

class Message
{
    /**
     * The sender's telephone number (see Message#recipient for documentation) or an alphanumeric
     * string of a maximum of 11 characters.
     *
     * @var string
     */
    private string $originator;

    /**
     * The telephone number of the recipient, consisting of the country code (e.g. '31' for The Netherlands),
     * the area/city code (e.g. '6' for Dutch mobile phones) and the subscriber number (e.g. '12345678').
     *
     * An example value would thus be 31612345678.
     *
     * @var string
     */
    private string $recipient;

    /**
     * @var string
     */
    private string $body;


    /**
     * @param string $originator
     * @param string $recipient
     * @param string $body
     * @throws DomainException Thrown when the originator or recipient is not formatted properly. See #originator,
     *                         #recipient.
     * @throws InvalidArgumentException
     */
    public function __construct(string $originator, string $recipient, string $body)
    {
        if (!preg_match('~^(\d+|[a-z0-9]{1,11})$~i', $originator)) {
            throw new DomainException(
                'Message originator is not a valid:'
                . ' must be a string of digits or a string consisting of 1-11 alphanumerical characters.'
            );
        }

        if (!preg_match('~^\d+$~', $recipient)) {
            throw new DomainException('Message recipient must consist of digits only.');
        }

        $this->originator = $originator;
        $this->recipient = $recipient;
        $this->body = $body;
    }


    /**
     * @return string
     */
    public function getOriginator(): string
    {
        return $this->originator;
    }


    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }


    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
