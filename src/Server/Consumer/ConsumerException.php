<?php

namespace Holkerveen\Pssg\Server\Consumer;

use RuntimeException;

class ConsumerException extends RuntimeException
{
	public static function create($socket, string $message): ConsumerException
	{
		$errcode = socket_last_error($socket);
		$errmsg = socket_strerror($errcode);
		return new ConsumerException("$message: $errcode: $errmsg");
	}
}
