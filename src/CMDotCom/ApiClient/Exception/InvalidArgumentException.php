<?php

namespace tvdijen\CMDotCom\ApiClient\Exception;

use InvalidArgumentException as BUILTIN_InvalidArgumentException;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;

class InvalidArgumentException extends BUILTIN_InvalidArgumentException implements ApiClientException
{
    /**
     * @param string $expected description of expected type
     * @param string $parameterName
     * @param mixed $parameter the parameter that is not of the expected type.
     *
     * @return self
     */
    public static function invalidType(string $expected, string $parameterName, $parameter)
    {
        $message = sprintf(
            'Invalid argument type: "%s" expected, "%s" given for "%s"',
            $expected,
            is_object($parameter) ? get_class($parameter) : gettype($parameter),
            $parameterName
        );

        return new self($message);
    }
}
