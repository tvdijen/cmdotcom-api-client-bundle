<?php

namespace tvdijen\CMDotCom\ApiClient\Messaging;

use tvdijen\CMDotCom\ApiClient\Exception\DomainException;
use tvdijen\CMDotCom\ApiClient\Exception\InvalidArgumentException;

use function array_map;
use function count;
use function in_array;
use function join;
use function sprintf;

class SendMessageResult
{
    public const ERROR_REQUEST_NOT_ALLOWED = 2;
    public const ERROR_MISSING_PARAMS = 9;
    public const ERROR_INVALID_PARAMS = 10;
    public const ERROR_NOT_FOUND = 20;
    public const ERROR_NOT_ENOUGH_BALANCE = 25;
    public const ERROR_API_NOT_FOUND = 98;
    public const ERROR_INTERNAL_ERROR = 99;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_BUFFERED = 'buffered';
    public const STATUS_SENT = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_DELIVERY_FAILED = 'delivery_failed';
    public const STATUS_NOT_SENT = 'not_sent';
    public const STATUS_UNKNOWN = 'unknown';

    /**
     * @var string
     */
    private string $deliveryStatus;

    /**
     * @var array[]
     */
    private array $errors;


    /**
     * @param string $deliveryStatus
     * @param array[] $errors
     * @throws \tvdijen\CMDotCom\ApiClient\Exception\InvalidArgumentException
     */
    public function __construct(string $deliveryStatus, array $errors)
    {
        if (!$this->isKnownDeliveryStatus($deliveryStatus)) {
            $deliveryStatus = self::STATUS_UNKNOWN;
        }

        $this->deliveryStatus = $deliveryStatus;
        $this->errors = $errors;
    }


    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return count($this->errors) === 0
            && in_array(
                $this->deliveryStatus,
                [self::STATUS_BUFFERED, self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_SCHEDULED]
            );
    }


    /**
     * @return bool
     */
    public function isMessageInvalid(): bool
    {
        return $this->hasErrorWithCode(self::ERROR_INVALID_PARAMS);
    }


    /**
     * @return bool
     */
    public function isAccessKeyInvalid(): bool
    {
        return $this->hasErrorWithCode(self::ERROR_REQUEST_NOT_ALLOWED);
    }


    /**
     * @return array[] Returns the errors returned by the API as an array of arrays with
     *                 keys int code, string description, string parameter.
     */
    public function getRawErrors(): array
    {
        return $this->errors;
    }


    /**
     * @return string E.g. '(#9) no (correct) recipients found; (#10) originator is invalid'
     */
    public function getErrorsAsString(): string
    {
        return join(
            '; ',
            array_map(function ($error) {
                    return sprintf('(#%d) %s', $error['code'], $error['description']);
                },
                $this->errors
            )
        );
    }


    /**
     * @param int $code
     * @return bool
     */
    private function hasErrorWithCode(int $code): bool
    {
        foreach ($this->errors as $error) {
            if (intval($error['code']) === $code) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param mixed $deliveryStatus
     * @return bool
     */
    private function isKnownDeliveryStatus($deliveryStatus): bool
    {
        return in_array(
            $deliveryStatus,
            [
                self::STATUS_SCHEDULED,
                self::STATUS_BUFFERED,
                self::STATUS_SENT,
                self::STATUS_DELIVERED,
                self::STATUS_DELIVERY_FAILED,
                self::STATUS_NOT_SENT
            ]
        );
    }
}
