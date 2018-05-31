<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Query\ICommands;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;

	interface ICompiler extends IConfigurable {
		/**
		 * compile query into native commands (one query could issue more commands)
		 *
		 * @param IQuery $query
		 *
		 * @return ICommands
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function compile(IQuery $query): ICommands;

		/**
		 * @param string $delimit
		 *
		 * @return string
		 */
		public function delimit(string $delimit): string;
	}
