<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class Commands extends SimpleObject implements ICommands {
		/** @var ICommand[] */
		protected $commands = [];

		/** @inheritdoc */
		public function addCommand(ICommand $command): ICommands {
			$this->commands[] = $command;
			return $this;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->commands;
		}
	}
