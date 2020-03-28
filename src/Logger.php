<?php

namespace Holkerveen\Pssg;

use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
  private string $target;
  private $handle;

  public function __construct(string $target)
  {
    $this->target = $target;
  }
  public function __destruct()
  {
    if($this->handle) fclose($this->handle);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function emergency($message, array $context = array())
  {
    $this->log(LogLevel::EMERGENCY, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function alert($message, array $context = array())
  {
    $this->log(LogLevel::ALERT, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function critical($message, array $context = array())
  {
    $this->log(LogLevel::CRITICAL, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function error($message, array $context = array())
  {
    $this->log(LogLevel::ERROR, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function warning($message, array $context = array())
  {
    $this->log(LogLevel::WARNING, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function notice($message, array $context = array())
  {
    $this->log(LogLevel::NOTICE, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function info($message, array $context = array())
  {
    $this->log(LogLevel::INFO, $message, $context);
  }

  /**
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function debug($message, array $context = array())
  {
    $this->log(LogLevel::DEBUG, $message, $context);
  }

  /**
   * @param mixed $level
   * @param string $message
   * @param array $context
   * @throws Exception
   */
  public function log($level, $message, array $context = array())
  {
    if (!$this->handle) $this->handle = fopen($this->target, 'a+b');
    if (!$this->handle) exit("Could not open log '$this->target'");

    $date = (new DateTime())->format("Y-m-d H:i:s");
    fputs($this->handle, "$date [$level] $message\n");
  }

}
