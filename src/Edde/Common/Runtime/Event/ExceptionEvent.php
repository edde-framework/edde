<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime\Event;

	/**
	 * When exception is thrown, this event is emitted.
	 */
	class ExceptionEvent extends RuntimeEvent {
		/**
		 * @var \Exception
		 */
		protected $exception;
		/**
		 * @var mixed
		 */
		protected $result;

		/**
		 * @param \Exception $exception
		 */
		public function __construct(\Exception $exception) {
			$this->exception = $exception;
		}

		/**
		 * @return \Exception
		 */
		public function getException(): \Exception {
			return $this->exception;
		}

		/**
		 * @param mixed $result
		 *
		 * @return $this
		 */
		public function result($result) {
			$this->result = $result;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getResult() {
			return $this->result;
		}

		/**
		 * @return bool
		 */
		public function hasResult(): bool {
			return $this->result !== null;
		}
	}
