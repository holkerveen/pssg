<?php

namespace Holkerveen\Pssg\Proc;

trait ForkTrait
{
	/** @var int[] */
	private static array $children = [];

	/** @var int[] */
	private static array $parents = [];

	public function fork(callable $c): void
	{
		$pidParent = getmypid();
		if ($pidParent === false) {
			exit("Could not get pid");
		}

		$pid = pcntl_fork();
		if ($pid == -1) {

			// Error
			exit("Could not fork");

		} elseif ($pid) {

			// Parent
			self::$children[] = $pid;
			if(count(self::$parents) === 0) {
				register_shutdown_function(function () {
					$pids = implode(", ", self::$children);
					echo "Waiting for child processes ($pids) to stop\n";
					foreach (self::$children as $pid) {
						pcntl_waitpid($pid, $status);
					}
					echo "Children finished\n";
				});
			}

			return;

		} else {

			// Child
			self::$parents[] = $pidParent;
			$c();
			exit();

		}
	}

}
