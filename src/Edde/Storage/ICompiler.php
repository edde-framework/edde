<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;

	interface ICompiler extends IConfigurable {
		/**
		 * compile query into string
		 *
		 * @param IQuery $query
		 *
		 * @return string
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function compile(IQuery $query): string;

		/**
		 * @param string $delimit
		 *
		 * @return string
		 */
		public function delimit(string $delimit): string;
	}
