<?php

namespace Holkerveen\Pssg\Server\Exception;

class NotImplemented extends HttpException
{
  public function getStatusCode(): int
  {
    return 501;
  }

  public function getStatusReason(): string
  {
    return "Not implemented";
  }
}
