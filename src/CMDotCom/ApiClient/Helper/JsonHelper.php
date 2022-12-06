<?php

namespace tvdijen\CMDotCom\ApiClient\Helper;

use tvdijen\CMDotCom\ApiClient\Exception\InvalidArgumentException;
use tvdijen\CMDotCom\ApiClient\Exception\JsonException;

use function is_string;
use function json_decode;
use function json_last_error;

final class JsonHelper
{
    /**
     * @psalm-return mixed
     */
    public static function decode(string $json)
    {
        static $jsonErrors = [
            JSON_ERROR_DEPTH          => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR      => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX         => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8           => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded',
        ];

        $data = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last         = json_last_error();
            $errorMessage = $jsonErrors[$last];

            if (!isset($errorMessage)) {
                $errorMessage = 'Unknown error';
            }

            throw JsonException::withMessage($errorMessage);
        }

        return $data;
    }
}
