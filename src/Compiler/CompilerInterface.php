<?php

namespace Holkerveen\Pssg\Compiler;

interface CompilerInterface
{
  public function parse(string $filename): string;

  public function getInputExtension(): string;

  public function getOutputExtension(): string;
}
