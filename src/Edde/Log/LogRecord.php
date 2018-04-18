<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Edde;

	/**
	 * Simple log record; holds record without any modifications.
	 */
	class LogRecord extends Edde implements ILogRecord {
		/**
		 * @var string
		 */
		protected $log;
		/**
		 * @var array
		 */
		protected $tags;

		/**
		 * A blonde rings up an airline.
		 * She asks, "How long are your flights from America to England?"
		 * The woman on the other end of the phone says, "Just a minute..."
		 * The blonde says, "Thanks!" and hangs up the phone.
		 *
		 * @param string $log
		 * @param array  $tags
		 */
		public function __construct($log, array $tags = null) {
			$this->log = $log;
			$this->tags = $tags;
		}

		/**
		 * @inheritdoc
		 */
		public function getLog() {
			return $this->log;
		}

		/**
		 * @inheritdoc
		 */
		public function getTags(): ?array {
			return $this->tags;
		}
	}
