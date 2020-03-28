<?php

namespace Holkerveen\Pssg\Compiler;

class Sass implements CompilerInterface
{
	private string $temporaryPath;

	public function setTemporaryPath(string $path) {
		$this->temporaryPath = $path;
	}

	public function parse(string $filename): string
	{
		echo "in:$filename\n";
		$sass = new \Sass();
		$css = $sass->compileFile($filename);
		echo "css:$css\n";
		return $css;
	}

	public function getInputExtension(): string
	{
		return 'scss';
	}

	public function getOutputExtension(): string
	{
		return 'css';
	}

}
