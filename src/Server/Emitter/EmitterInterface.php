<?php

namespace Holkerveen\Pssg\Server\Emitter;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
	public function emit(ResponseInterface $response): void;
}
