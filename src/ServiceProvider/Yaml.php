<?php

namespace Holkerveen\Pssg\ServiceProvider;

class Yaml
{
  public function parse(string $in): array
  {
    return spyc_load_file($in);
  }
}
