<?php

namespace Holkerveen\Pssg\Server\Emitter;

use RuntimeException;

class EmitterException extends RuntimeException
{
	public static function create($socket, string $message): EmitterException
	{
		$errcode = socket_last_error($socket);
		$errmsg = socket_strerror($errcode);
		return new EmitterException("$message: $errcode: $errmsg");
	}
}
