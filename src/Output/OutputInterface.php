<?php

namespace Holkerveen\Pssg\Output;

use Holkerveen\Pssg\Server\Response;

interface OutputInterface
{
  public function get(string $path): Response;
  public function set(string $path, string $content): void;
  public function has(string  $path): bool;
  public function remove(string $path): void;
}
