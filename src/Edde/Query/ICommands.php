<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use IteratorAggregate;
	use Traversable;

	interface ICommands extends IteratorAggregate {
		/**
		 * @param ICommand $command
		 *
		 * @return ICommands
		 */
		public function addCommand(ICommand $command): ICommands;

		/**
		 * @return Traversable|ICommand[]
		 */
		public function getIterator();
	}
