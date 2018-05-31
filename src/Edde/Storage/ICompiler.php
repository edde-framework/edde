<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\ICommands;
	use Edde\Query\IQuery;

	interface ICompiler {
		/**
		 * compile query into native commands (one query could issue more commands)
		 *
		 * @param IQuery $query
		 *
		 * @return ICommands
		 */
		public function compile(IQuery $query): ICommands;
	}
