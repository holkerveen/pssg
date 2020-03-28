<?php

namespace Holkerveen\Pssg;

use Exception;
use Holkerveen\Pssg\Compiler\CompilerInterface;
use Holkerveen\Pssg\Output\OutputInterface;
use Holkerveen\Pssg\Proc\ForkInterface;
use Holkerveen\Pssg\Proc\ForkTrait;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class Compiler implements ForkInterface, InotifyInterface
{
	use ForkTrait;
	use InotifyTrait;

	private $path;
	private $inotify;
	private OutputInterface $output;

	/**
	 * @var CompilerInterface[]
	 */
	private array $compilers;

	public function __construct(string $path)
	{
		$this->path = realpath($path);
		if (!is_dir($this->path)) {
			throw new RuntimeException("'$path' is not a directory");
		}
		if (!is_file("$this->path/index.md")) {
			throw new RuntimeException("'$path' does not contain an index.md file");
		}
	}

	/**
	 * @param CompilerInterface[] $compilers
	 * @return Compiler
	 * @throws Exception
	 */
	public function setCompilers(array $compilers): Compiler
	{
		$this->compilers = [];
		foreach ($compilers as $compiler) {
			if (!$compiler instanceof CompilerInterface) {
				throw new Exception("Not a CompilerInterface $compiler");
			}
			$this->compilers[$compiler->getInputExtension()] = $compiler;
		}
		return $this;
	}

	public function setOutput(OutputInterface $output)
	{
		$this->output = $output;
	}

	public function watch()
	{
		$this->inotifyInit();
		foreach ($this->getInputDirs() as $dir) {
			$this->inotifyWatch($dir);
		}

		$this->fork(function () {
			while (1) {
				foreach ($this->inotifyRead() as $event) {
					$mask = $event['mask'];
					$path = "{$this->inotifyGetPath($event['wd'])}/{$event['name']}";
					while ($mask > 0) {
						if ($mask & IN_CREATE) {
							$mask -= IN_CREATE;
							echo "CREATE $path\n";
							$this->buildFile($path);
							continue;
						} elseif ($mask & IN_MODIFY) {
							$mask -= IN_MODIFY;
							echo "MODIFY $path\n";
							$this->buildFile($path);
							continue;
						} elseif ($mask & IN_MOVE) {
							$mask -= IN_MOVE;
							var_dump($event, $this->inotifyGetPath($event['wd']));
							continue;
						} elseif ($mask & IN_DELETE) {
							$mask -= IN_DELETE;
							echo "DELETE $path\n";
							$this->deleteFile($path);
							continue;
						} elseif ($mask & IN_DELETE_SELF) {
							$mask -= IN_DELETE_SELF;
							echo "DELETE_SELF $path\n";
							$this->deleteFile($path);
							continue;
						} elseif ($mask & IN_MOVE_SELF) {
							$mask -= IN_MOVE_SELF;
							var_dump($event, $this->inotifyGetPath($event['wd']));
							continue;
						} elseif ($mask & IN_ISDIR) {
							$mask -= IN_ISDIR;
							var_dump($event, $this->inotifyGetPath($event['wd']));
							continue;
						} else {
							throw new Exception("Unhandled inotify event {$mask}");
						}
					}

				}
			}
		});

	}

	/**
	 * @param string $in
	 * @throws Exception
	 */
	private function deleteFile(string $in): void
	{
		if (!preg_match('/\.([a-zA-Z0-9]+)$/', basename($in), $matches)) {
			return;
		}
		$extension = $matches[1];
		echo "deleteFile $in\n";

		if (!isset($this->compilers[$extension])) {
			throw new Exception("No compiler for extension '$extension'");
		}
		$compiler = $this->compilers[$extension];

		$out = substr($in, strlen($this->path));
		$out = preg_replace(
			'/\.' . preg_quote($extension) . '$/',
			'.' . $compiler->getOutputExtension(),
			$out
		);

		echo "Out = $out\n";
		if ($this->getOutput()->has($out)) {
			echo "Removing $out\n";
			$this->getOutput()->remove($out);
		}
	}

	/**
	 * @param string $in
	 * @throws Exception
	 */
	private function buildFile(string $in): void
	{
		if (!preg_match('/\.([a-zA-Z0-9]+)$/', basename($in), $matches)) {
			return;
		}
		$extension = $matches[1];

		if (!isset($this->compilers[$extension])) {
			return;
		}
		$compiler = $this->compilers[$extension];

		if (!is_file($in)) {
			throw new Exception("Not a file: $in");
		}

		$out = substr($in, strlen($this->path));
		$out = preg_replace(
			'/\.' . preg_quote($extension) . '$/',
			'.' . $compiler->getOutputExtension(),
			$out
		);

		$this->getOutput()->set($out, $compiler->parse($in));
	}

	/**
	 * @throws Exception
	 */
	public function build()
	{
		foreach ($this->getInputDirs() as $dir) {
			foreach (glob("$dir/*.*") as $in) {
				$this->buildFile($in);
			}
		}
	}

	public function getOutput(): OutputInterface
	{
		return $this->output;
	}

	/**
	 * @return string[]
	 */
	private function getInputDirs(): array
	{
		$dir = new RecursiveDirectoryIterator($this->path);
		$it = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
		$result = [];

		$result[] = $this->path;

		foreach ($it as $file) {
			if (in_array(basename($file), ['.', '..'])) {
				continue;
			}
			if (is_file($file)) {
				continue;
			}
			$result[] = (string)$file;
		}

		return $result;
	}

}
