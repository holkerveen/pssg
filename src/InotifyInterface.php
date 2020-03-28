<?php


namespace Holkerveen\Pssg;


interface InotifyInterface
{

  public function inotifyWatch(string $path, int $param): int;

  public function inotifyRead(): array;
}
