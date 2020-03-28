<?php


namespace Holkerveen\Pssg\Server;


use Exception;
use Psr\Http\Message\StreamInterface;

class MessageBody implements StreamInterface
{

	private string $body;
	private int $pos;

	public function __construct(string $body = "")
	{
		$this->body = $body;
	}

	/**
	 * @inheritDoc
	 */
	public function __toString()
	{
		return $this->body;
	}

	/**
	 * @inheritDoc
	 */
	public function close()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function detach()
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getSize()
	{
		return strlen($this->body);
	}

	/**
	 * @inheritDoc
	 */
	public function tell()
	{
		return $this->pos;
	}

	/**
	 * @inheritDoc
	 */
	public function eof()
	{
		return $this->pos = strlen($this->body);
	}

	/**
	 * @inheritDoc
	 */
	public function isSeekable()
	{
		return true;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		switch ($whence) {
			case SEEK_SET:
				$pos = $offset;
				break;
			case SEEK_CUR:
				$pos = $this->pos + $offset;
				break;
			case SEEK_END:
				$pos = strlen($this->body) + $offset;
				break;
		}
		if ($pos < 0 || $pos > strlen($this->body)) {
			throw new Exception("Seek outside of range");
		}
		$this->pos = $pos;
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

	/**
	 * @inheritDoc
	 */
	public function isWritable()
	{
		return false;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function write($string)
	{
		throw new Exception("Not writable");
	}

	/**
	 * @inheritDoc
	 */
	public function isReadable()
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function read($length)
	{
		$r = substr($this->body, $this->pos, $length);
		$this->pos += strlen($r);
		return $r;
	}

	/**
	 * @inheritDoc
	 */
	public function getContents()
	{
		$r = substr($this->body, $this->pos);
		$this->pos = strlen($this->body);
		return $r;
	}

	/**
	 * @inheritDoc
	 */
	public function getMetadata($key = null)
	{
		return [
			'timed_out' => false,
			'blocked' => true,
			'eof' => $this->eof(),
			'unread_bytes' => strlen($this->body) - $this->pos,
			'stream_type' => 'string',
			'wrapper_type' => 'custom',
			'wrapper_data' => [],
			'mode' => 'rb',
			'seekable' => $this->isSeekable(),
			'uri' => null,
		];
	}
}
