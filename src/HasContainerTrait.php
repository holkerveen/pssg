<?php


namespace Holkerveen\Pssg;


use Psr\Container\ContainerInterface;

trait HasContainerTrait
{
  protected ContainerInterface $container;

  public function setContainer(ContainerInterface $container)
  {
    $this->container = $container;
  }

}
