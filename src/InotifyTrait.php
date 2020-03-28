<?php

namespace Holkerveen\Pssg;

trait InotifyTrait
{
  private $inotifyHandle = null;
  private array $inotifyPaths = [];
  public int $inotifyDefaultMask = IN_MODIFY | IN_CREATE | IN_MOVE | IN_DELETE | IN_DELETE_SELF | IN_MOVE_SELF;

  public function inotifyInit(): void
  {
    $this->inotifyHandle = inotify_init();
    $read = [$this->inotifyHandle];
    $write = null;
    $except = null;
    stream_select($read, $write, $except, 0);
    stream_set_blocking($this->inotifyHandle, true);
  }

  public function inotifyWatch(string $path, ?int $mask = null): int
  {
    $wd = inotify_add_watch($this->inotifyHandle, $path, $mask ?? $this->inotifyDefaultMask);
    $this->inotifyPaths[$wd] = $path;
    return $wd;
  }

  public function inotifyRead(): array
  {
    return inotify_read($this->inotifyHandle);
  }

  private function inotifyGetPath(string $wd): string
  {
    return $this->inotifyPaths[$wd];
  }
}
