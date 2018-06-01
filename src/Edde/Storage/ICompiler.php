<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Query\ICommand;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;

	interface ICompiler extends IConfigurable {
		/**
		 * compile query into native command
		 *
		 * @param IQuery $query
		 * @param array  $params
		 *
		 * @return ICommand
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function compile(IQuery $query, array $params = []): ICommand;

		/**
		 * @param string $delimit
		 *
		 * @return string
		 */
		public function delimit(string $delimit): string;
	}
