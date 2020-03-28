<?php

namespace Holkerveen\Pssg\Server\Exception;

use Exception;

abstract class HttpException extends Exception
{
  abstract public function getStatusCode(): int;

  abstract public function getStatusReason(): string;
}
