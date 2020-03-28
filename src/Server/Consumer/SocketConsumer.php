<?php

namespace Holkerveen\Pssg\Server\Consumer;

use Holkerveen\Pssg\Server\Exception\NotImplemented;
use Holkerveen\Pssg\Server\Request;
use Psr\Http\Message\RequestInterface;

class SocketConsumer implements ConsumerInterface
{
	private $socket;
	private int $connectionId;

	/**
	 * SocketConsumer constructor.
	 * @param $socket resource
	 */
	public function __construct($socket, int $connectionId)
	{
		$this->socket = $socket;
		$this->connectionId = $connectionId;
	}

	/**
	 * @return RequestInterface|null
	 * @throws NotImplemented
	 */
	public function consume(): ?RequestInterface
	{
		$msg = "";
		$header = null;
		$body = null;
		while (true) {
			$received = socket_recv($this->socket, $data, 100, 0);
			if ($received === false) {
				throw ConsumerException::create($this->socket,
					"Could not receive data on connection #$this->connectionId");
			}
//			echo "#$this->connectionId received $received bytes: \n";
			if ($received === 0 && $msg === "") {
				socket_close($this->socket);
				return null;
			}
			$msg .= $data;

			// Don't care about body so stop reading if end of header is received
			$searchStart = max(strlen($msg) - strlen($data) - 4, 0);
			if (strstr(substr($msg, $searchStart), "\r\n\r\n") !== false) {
				echo "#$this->connectionId header fully received\n";
				break;
			}
		}

		return Request::createFromString($msg);
	}
}
