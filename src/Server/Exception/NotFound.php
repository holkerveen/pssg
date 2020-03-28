<?php

namespace Holkerveen\Pssg\Server\Exception;

class NotFound extends HttpException
{
  public function getStatusCode(): int
  {
    return 404;
  }

  public function getStatusReason(): string
  {
    return "File not found";
  }
}
