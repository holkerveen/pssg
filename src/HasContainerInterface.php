<?php

namespace Holkerveen\Pssg;

use Psr\Container\ContainerInterface;

interface HasContainerInterface
{
  public function setContainer(ContainerInterface $container);
}
