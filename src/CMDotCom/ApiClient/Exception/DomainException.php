<?php

namespace tvdijen\CMDotCom\ApiClient\Exception;

use DomainException as BuiltinDomainException;

class DomainException extends BuiltinDomainException implements ApiClientException
{
}
