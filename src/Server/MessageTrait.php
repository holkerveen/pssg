<?php

namespace Holkerveen\Pssg\Server;

use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
  private string $protocolVersion;
  private array $headers = [];
  private StreamInterface $body;

  /**
   * @inheritDoc
   */
  public function getProtocolVersion()
  {
    return $this->protocolVersion;
  }

  /**
   * @inheritDoc
   */
  public function withProtocolVersion($version)
  {
    $request = clone $this;
    $request->protocolVersion = $version;
    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * @inheritDoc
   */
  public function hasHeader($name)
  {
    return count(array_filter(array_keys($this->headers), function ($headerName) use ($name) {
        return strcasecmp($headerName, $name) === 0;
      })) > 0;
  }

  /**
   * @inheritDoc
   */
  public function getHeader($name)
  {
    $values = [];
    foreach ($this->headers as $headerName => $headerValues) {
      if (strcasecmp($name, $headerName) === 0) $values = array_merge($values, $headerValues);
    }
    return $values;
  }

  /**
   * @inheritDoc
   */
  public function getHeaderLine($name)
  {
    return implode(",", $this->getHeader($name));
  }

  /**
   * @inheritDoc
   */
  public function withHeader($name, $value)
  {
    return $this->withoutHeader($name)->withAddedHeader($name, $value);
  }

  /**
   * @inheritDoc
   */
  public function withAddedHeader($name, $value)
  {
    $request = clone $this;
    if (is_string($value)) {
      $request->headers[$name][] = $value;
    } else {
      $request->headers[$name] = array_merge(
        array_keys($request->getHeader($name)),
        array_keys($value)
      );
    }
    return $request;
  }

  /**
   * @inheritDoc
   */
  public function withoutHeader($name)
  {
    $request = clone $this;
    $keys = array_keys($request->headers);
    array_walk($keys, function ($headerName) use ($name, $request) {
      if (strcasecmp($name, $headerName) === 0) unset($request[$headerName]);
    });
    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * @inheritDoc
   * @return static
   */
  public function withBody(StreamInterface $body)
  {
    $request = clone $this;
    $request->body = $body;
    return $request;
  }
}
