<?php

namespace Holkerveen\Pssg\Compiler;

use Exception;

class Typescript implements CompilerInterface
{
	private string $temporaryPath;

	public function setTemporaryPath(string $path):Typescript {
		$this->temporaryPath = $path;
		return $this;
	}

	/**
	 * @param string $filename
	 * @return string
	 * @throws Exception
	 */
	public function parse(string $filename): string
	{
		$id = uniqid();
		$outFile = "{$this->temporaryPath}/{$id}.js";
		$filename = realpath($filename);
		exec("tsc --outFile '$outFile' --target es5 --lib DOM,ES6,DOM.Iterable,ScriptHost '$filename'", $output, $returnvar);
		if($returnvar !== 0) {
			throw new Exception("Error compiling typescript file '$filename'");
		}
		$result = file_get_contents($outFile);
		unlink($outFile);
		return $result;
	}

	public function getInputExtension(): string
	{
		return 'ts';
	}

	public function getOutputExtension(): string
	{
		return 'js';
	}
}
