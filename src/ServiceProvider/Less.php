<?php

namespace Holkerveen\Pssg\ServiceProvider;

use Parsedown;

class Less implements ParserInterface
{
  public function parse(string $in): string
  {
    static $parser;
    if (!$parser) {
      $parser = new Parsedown();
      $parser->setSafeMode(true);
    }
    return $parser->text($in);
  }
}
