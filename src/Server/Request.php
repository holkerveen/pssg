<?php

namespace Holkerveen\Pssg\Server;

use Holkerveen\Pssg\Server\Exception\NotImplemented;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
  use MessageTrait;

  private string $method;
  private string $uri;
  private string $requestTarget;

	/**
	 * @param string $msg
	 * @return MessageTrait|static
	 * @throws NotImplemented
	 */
  public static function createFromString(string $msg)
  {
    $request = new static;

    $lines = explode("\r\n", $msg);

    [$method, $uri, $version] = explode(" ", trim(array_shift($lines)));

    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) throw new NotImplemented();
    $request->method = $method;

    if (!preg_match('#^HTTP/([0-9]+)\.([0-9]+)$#', $version, $matches)) {
      $request->protocolVersion = ((int)$matches[1]) . "." . ((int)$matches[2]);
    }

    $request->uri = $uri;

    while (($line = trim(array_shift($lines))) !== "") {
      [$name, $value] = explode(":", $line);
      $request = $request->withHeader(trim($name),trim($value));
    }

    $request->body = new MessageBody(implode("\r\n",$lines));

    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getRequestTarget()
  {
    return $this->requestTarget ?? $this->uri;
  }

  /**
   * @inheritDoc
   */
  public function withRequestTarget($requestTarget)
  {
    $request = clone $this;
    $request->requestTarget = $requestTarget;
    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * @inheritDoc
   */
  public function withMethod($method)
  {
    $request = clone $this;
    $request->method = $method;
    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getUri()
  {
    return $this->uri;
  }

  /**
   * @inheritDoc
   */
  public function withUri(UriInterface $uri, $preserveHost = false)
  {
    $request = clone $this;
    $request->uri = $uri;
    return $request;
  }

}
