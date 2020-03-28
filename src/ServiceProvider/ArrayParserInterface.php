<?php

namespace Holkerveen\Pssg\ServiceProvider;

interface ArrayParserInterface
{
  /**
   * @param string $in
   * @return array
   */
  public function parse(string $in): array;
}
