<?php

namespace Holkerveen\Pssg\Server\Emitter;

use Psr\Http\Message\ResponseInterface;

class SocketEmitter implements EmitterInterface
{
	private $socket;

	/**
	 * SocketEmitter constructor.
	 * @param $socket resource
	 */
	public function __construct($socket)
	{
		$this->socket = $socket;
	}

	/**
	 * @param ResponseInterface $response
	 * @return bool
	 * @throws EmitterException
	 */
	public function emit(ResponseInterface $response): void
	{
		$written = socket_write($this->socket, sprintf("HTTP/%s %s %s\r\n",
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		));
		if ($written === false) {
			throw EmitterException::create($this->socket, "Failed to write status line to connection");
		}

		foreach ($response->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				$written = socket_write($this->socket, sprintf("%s: %s\r\n", $name, $value));
				if ($written === false) {
					throw EmitterException::create($this->socket, "Failed to write header '$name' to connection");
				}
			}
		}

		if ($response->getBody() !== "") {
			$written = socket_write($this->socket, "\r\n" . $response->getBody());
			if ($written === false) {
				throw EmitterException::create($this->socket, "Failed to write body to connection");
			}
		}
	}
}
