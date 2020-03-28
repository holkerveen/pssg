<?php

namespace Holkerveen\Pssg;

use Exception;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
  private array $defined;
  private array $instantiated;

  public function bootstrap(array $services)
  {
    $this->defined = $services;
  }

  /**
   * @param $id
   * @return mixed
   * @throws Exception
   */
  public function get($id)
  {
    if (!$this->has($id)) throw new Exception("Could not find service '$id'");
    if (!isset($this->instantiated[$id])) $this->instantiated[$id] = ($this->defined[$id])();
    return $this->instantiated[$id];
  }

  /**
   * @param $id
   * @return bool
   */
  public function has($id)
  {
    return isset($this->defined[$id]);
  }
}
