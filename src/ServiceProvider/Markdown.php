<?php

namespace Holkerveen\Pssg\ServiceProvider;

use Parsedown;

class Markdown implements ParserInterface
{
  public function parse(string $in): string
  {
    static $parser;
    if (!$parser) {
      $parser = new Parsedown();
    }
    return $parser->text($in);
  }
}
