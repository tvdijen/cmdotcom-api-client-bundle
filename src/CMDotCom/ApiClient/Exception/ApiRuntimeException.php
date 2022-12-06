<?php

namespace tvdijen\CMDotCom\ApiClient\Exception;

use Exception;

use function array_map;
use function join;
use function sprintf;

class ApiRuntimeException extends RuntimeException
{
    /**
     * @param string $message
     * @param array $errors The original array of error messages as produced by MessageBird.
     * @param \Exception|null $previous
     * @param int $code
     */
    public function __construct(string $message, array $errors, Exception $previous = null, int $code = 0)
    {
        $message = sprintf('%s %s', $message, $this->createErrorString($errors));

        parent::__construct($message, $code, $previous);
    }


    /**
     * @param array $errors
     * @return string E.g. (#9) no (correct) recipients found; (#10) originator is invalid
     */
    private function createErrorString(array $errors): string
    {
        return join(
            '; ',
            array_map(function ($error) {
                    return sprintf('(#%d) %s', $error['code'], $error['description']);
                },
                $errors
            )
        );
    }
}
