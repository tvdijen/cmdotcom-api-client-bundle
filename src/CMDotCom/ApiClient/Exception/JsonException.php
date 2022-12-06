<?php

namespace tvdijen\CMDotCom\ApiClient\Exception;

use function sprintf;

final class JsonException extends RuntimeException
{
    public static function withMessage(string $errorMessage): JsonException
    {
        return new self(sprintf('Unable to parse JSON data: %s', $errorMessage));
    }
}
