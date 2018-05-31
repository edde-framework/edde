<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;
	use function array_merge;
	use function iterator_to_array;

	class Commands extends SimpleObject implements ICommands {
		/** @var ICommand[] */
		protected $commands = [];

		/** @inheritdoc */
		public function addCommand(ICommand $command): ICommands {
			$this->commands[] = $command;
			return $this;
		}

		/** @inheritdoc */
		public function addCommands(ICommands $commands): ICommands {
			$this->commands = array_merge($this->commands, iterator_to_array($commands));
			return $this;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->commands;
		}
	}
