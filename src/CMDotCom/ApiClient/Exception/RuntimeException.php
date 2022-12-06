<?php

namespace tvdijen\CMDotCom\ApiClient\Exception;

use RuntimeException as BUILTIN_RuntimeException;

class RuntimeException extends BUILTIN_RuntimeException implements ApiClientException
{
}
