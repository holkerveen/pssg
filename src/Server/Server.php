<?php

namespace Holkerveen\Pssg\Server;

use Exception;
use Holkerveen\Pssg\Output\OutputInterface;
use Holkerveen\Pssg\Proc\ForkInterface;
use Holkerveen\Pssg\Proc\ForkTrait;
use Holkerveen\Pssg\Server\Consumer\SocketConsumer;
use Holkerveen\Pssg\Server\Emitter\SocketEmitter;
use Holkerveen\Pssg\Server\Exception\HttpException;

function createSocketException($socket, string $message)
{
	$errcode = socket_last_error($socket);
	$errmsg = socket_strerror($errcode);
	return new Exception("$message: $errcode: $errmsg");
}

class Server implements ForkInterface
{
	use ForkTrait;

	/**
	 * @return false|resource
	 * @throws Exception
	 */
	private function setupPrimarySocket()
	{
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			throw createSocketException($socket, "Could not create socket");
		}
		echo "Socket created\n";

		if (socket_bind($socket, $address = '0.0.0.0', $port = 80) === false) {
			throw createSocketException($socket, "Could not bind socket to $address:$port");
		}
		echo "Socket bound\n";

		if (socket_listen($socket) === false) {
			throw createSocketException($socket, "Could listen on socket");
		}
		echo "Socket listening\n";

		if (socket_getsockname($socket, $address, $port) === false) {
			throw createSocketException($socket, "Could not get socket name");
		}
		echo "Listening on $address:$port\n";

		return $socket;
	}

	/**
	 * @param OutputInterface $output
	 * @throws Exception
	 */
	public function serve(OutputInterface $output)
	{
		error_reporting(E_ALL);
		$socket = $this->setupPrimarySocket();

		$connectionId = 0;
		while (true) {
			$connectionId++;
			$connection = @socket_accept($socket);
			if ($connection === false) {
				throw createSocketException($socket, "Could not accept connection");
			}
			$this->fork(function () use ($connection, $connectionId, $output) {
				if (socket_getpeername($connection, $remote_address, $remote_port) === false) {
					throw createSocketException($connection, "Could not get peer address or port from connection");
				}
				echo "Accepted connection #$connectionId from $remote_address:$remote_port\n";

				/********************************************
				 * Receive
				 ********************************************/

				try {
					$request = (new SocketConsumer($connection, $connectionId))->consume();
					if ($request) {
						$path = parse_url($request->getUri(), PHP_URL_PATH);
						if ($path === "/") {
							$path = "/index.html";
						}
						if (!preg_match('/\.[a-zA-Z0-9]+$/', basename($path))) {
							$path .= '.html';
						}

						$response = $output->get($path);
						echo " < $remote_address:$remote_port {$request->getMethod()} {$request->getUri()}\n";
					} else {
						echo " < $remote_address:$remote_port (no request parsed)\n";
					}
				} catch (HttpException $e) {
					$response = Response::create($e->getStatusCode(), $e->getStatusReason());
				}


				/********************************************
				 * Respond
				 ********************************************/
				if(isset($response)) {
					echo " > {$response->getStatusCode()} {$response->getReasonPhrase()}\n";
					(new SocketEmitter($connection))->emit($response);
				}

				socket_shutdown($connection, 2);
				socket_close($connection);
				$connection = null;
				echo "Closed connection\n";
			});
		}

		socket_close($socket);
		echo "Closed socket\n";
	}

}
