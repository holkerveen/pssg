<?php

namespace Holkerveen\Pssg\Proc;

interface ForkInterface
{
	public function fork(callable $c): void;
}
