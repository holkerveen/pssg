<?php

namespace Holkerveen\Pssg\Server;

use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface
{
  use MessageTrait;

  private int $code;
  private string $reason;

  public static function create(int $code, string $reason): Response
  {
    $response = new static;
    $response->code = $code;
    $response->reason = $reason;
    $response->protocolVersion = "1.1";
    $response->body = new MessageBody();
    return $response;
  }

  /**
   * @inheritDoc
   */
  public function getStatusCode()
  {
    return $this->code;
  }

  /**
   * @inheritDoc
   */
  public function withStatus($code, $reasonPhrase = '')
  {
    $response = clone $this;
    $response->code = $code;
    $response->reason = $reasonPhrase;
    return $response;
  }

  /**
   * @inheritDoc
   */
  public function getReasonPhrase()
  {
    return $this->reason;
  }
}
