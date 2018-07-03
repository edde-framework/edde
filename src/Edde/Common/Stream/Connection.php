<?php
	declare(strict_types=1);

	namespace Edde\Common\Stream;

	use Edde\Api\Stream\ConnectionException;
	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IConnector;
	use Edde\Api\Stream\IStreamServer;
	use Edde\Common\Object;

	class Connection extends Object implements IConnection {
		/**
		 * @var IStreamServer
		 */
		protected $connector;
		/**
		 * @var resource
		 */
		protected $stream;
		/**
		 * @var string
		 */
		protected $id;

		/**
		 * A woman decided to have a face lift for her birthday.
		 * She spent $5000 and felt really good about the results.
		 * On her way home she stopped at a dress shop to look around.
		 * As she was leaving, she said to the sales clerk, "I hope you don't mind me asking, but how old do you think I am?"
		 * "About 35,"he replied.
		 * "I'm actually 47," the woman said, feeling really happy.
		 * After that she went into McDonald's for lunch and asked the order taker the same question.
		 * He replied, "Oh, you look about 29."
		 * "I am actually 47!" she said, feeling really good.
		 * While standing at the bus stop she asked an old man the same question.
		 * He replied, "I am 85 years old and my eyesight is going. But when I was young there was a sure way of telling a woman's age. If I put my hand up your skirt I will be able to tell your exact age."
		 * There was no one around, so the woman said, "What the hell?" and let him slip his hand up her skirt.
		 * After feeling around for a while, the old man said, "OK, You are 47."
		 * Stunned, the woman said, "That was brilliant! How did you do that?"
		 * The old man replied, "I was behind you in line at McDonald's."
		 *
		 * @param IConnector $connector
		 * @param resource   $stream
		 * @param string     $id
		 */
		public function __construct(IConnector $connector, $stream, string $id) {
			$this->connector = $connector;
			$this->stream = $stream;
			$this->id = $id;
		}

		public function getId(): string {
			return $this->id;
		}

		public function getStream() {
			if ($this->stream === null) {
				throw new ConnectionException('Dead connection.');
			}
			return $this->stream;
		}

		public function isAlive(): bool {
			return $this->stream !== null;
		}

		public function read(callable $handler = null): string {
			/**
			 * stream must read the data or connection will stay forever
			 */
			$data = '';
			$read = $except = null;
			$connectionList = [$this->stream];
			while (stream_select($read, $connectionList, $except, 256000) && feof($stream = reset($connectionList)) === false) {
				if (($source = fread($stream, 8192)) === '') {
					break;
				}
				if ($handler) {
					$handler($source);
					continue;
				}
				$data .= $source;
			}
			return $data;
		}

		public function write(string $buffer): IConnection {
			$limit = 8192;
			$length = strlen($buffer);
			$count = 0;
			$read = $except = null;
			$connectionList = [$this->stream];
			while ($count < $length) {
				if (stream_select($read, $connectionList, $except, 256000) > 0) {
					$count += fwrite(reset($connectionList), substr($buffer, $count, $limit), $limit);
				}
			}
			return $this;
		}

		public function close(): IConnection {
			fflush($this->stream);
			stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
			fclose($this->stream);
			$this->stream = null;
			return $this;
		}
	}
