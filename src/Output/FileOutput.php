<?php

namespace Holkerveen\Pssg\Output;

use Exception;
use Holkerveen\Pssg\Server\MessageBody;
use Holkerveen\Pssg\Server\Response;
use RuntimeException;

class FileOutput implements OutputInterface
{
	private string $outputDir;

	public function __construct(string $outputDir)
	{
		if (!in_array($outputDir[0], ['.', '/'])) {
			$outputDir = getcwd() . '/' . $outputDir;
		}
		if (!is_dir($outputDir)) {
			if (mkdir($outputDir,0777, true)) {
				echo "Created output directory '$outputDir'\n";
			} else {
				throw new RuntimeException("Output dir must be a directory: '$outputDir'");
			}
		}
		$this->outputDir = realpath($outputDir);
	}

	/**
	 * @param string $path
	 * @return Response
	 * @throws Exception
	 */
	public function get(string $path): Response
	{
		$this->checkPathIsValid($path);
		if ($this->has($path)) {
			return Response::create(200, "OK")->withBody(new MessageBody(file_get_contents("{$this->outputDir}{$path}")));
		} else {
			return Response::create(404, "File not found");
		}

	}

	/**
	 * @param string $path
	 * @param string $content
	 * @throws Exception
	 */
	public function set(string $path, string $content): void
	{
		$this->checkPathIsValid($path);
		$path = "{$this->outputDir}{$path}";
		if (!is_dir(dirname($path)) && is_dir($this->outputDir)) {
			mkdir(dirname($path), 0777, true);
		}
		file_put_contents($path, $content);
		echo strlen($content) . "B > $path\n";
	}

	/**
	 * @param string $path
	 * @return bool
	 * @throws Exception
	 */
	public function has(string $path): bool
	{
		$this->checkPathIsValid($path);
		$path = "{$this->outputDir}{$path}";
		return file_exists($path) && is_file($path) && is_readable($path);
	}

	/**
	 * @param string $path
	 * @throws Exception
	 */
	public function remove(string $path): void
	{
		$this->checkPathIsValid($path);
		$path = "{$this->outputDir}{$path}";

		if(!is_file($path)) throw new Exception(("Path is not a file: '$path'"));
		unlink($path);

		$dir = dirname($path);
		while($dir !== $this->outputDir && self::isEmptyDir($dir)) {
			unlink($dir);
			$dir = dirname($dir);
		}
	}

	/**
	 * @param $path
	 * @throws Exception
	 */
	private function checkPathIsValid($path)
	{
		if ($path[0] !== '/') {
			throw new Exception("Can only store realpaths starting with '/'");
		}
		if (preg_match('#/\.{1,2}/#', $path)) {
			throw new Exception("Can only store realpaths");
		}
		if (preg_match('#/{2,*}#', $path)) {
			throw new Exception("Can only store realpaths");
		}
	}

	private static function isEmptyDir($dir) {
		$handle = opendir($dir);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				closedir($handle);
				return FALSE;
			}
		}
		closedir($handle);
		return TRUE;
	}
}
