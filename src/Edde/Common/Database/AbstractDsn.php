<?php
	declare(strict_types=1);

	namespace Edde\Common\Database;

	use Edde\Api\Database\IDsn;
	use Edde\Common\Object\Object;

	abstract class AbstractDsn extends Object implements IDsn {
		/**
		 * @var string
		 */
		protected $dsn;
		protected $optionList = [];

		/**
		 * Two students talk:
		 * "What are you reading?"
		 * "Quantum physics theory book."
		 * "But why are you reading it upside-down?"
		 * "It makes no difference anyway."
		 *
		 * @param string $dsn
		 * @param array  $optionList
		 */
		public function __construct(string $dsn, array $optionList = []) {
			$this->dsn = $dsn;
			$this->optionList = $optionList;
		}

		public function getDsn(): string {
			return $this->dsn;
		}

		/**
		 * @inheritdoc
		 */
		public function setOption(string $option, $value): IDsn {
			$this->optionList[$option] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getOption(string $option, $default = null) {
			return $this->optionList[$option] ?? $default;
		}

		/***
		 * @inheritdoc
		 */
		public function getOptionList(): array {
			return $this->optionList;
		}
	}
