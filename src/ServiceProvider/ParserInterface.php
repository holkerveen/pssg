<?php

namespace Holkerveen\Pssg\ServiceProvider;

interface ParserInterface
{
  public function parse(string $in): string;
}
