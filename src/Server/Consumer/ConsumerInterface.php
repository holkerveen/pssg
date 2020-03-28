<?php

namespace Holkerveen\Pssg\Server\Consumer;

use Psr\Http\Message\RequestInterface;

interface ConsumerInterface
{
	public function consume(): ?RequestInterface;
}
