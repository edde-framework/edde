<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\Commands;
	use Edde\Query\ICommands;
	use Edde\Query\IQuery;

	class PdoCompiler extends AbstractCompiler {
		/** @var string */
		protected $delimiter;

		/**
		 * @param string $delimiter
		 */
		public function __construct(string $delimiter) {
			$this->delimiter = $delimiter;
		}

		/** @inheritdoc */
		public function compile(IQuery $query): ICommands {
			$commands = new Commands();
			return $commands;
		}
	}
