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
		public function query(IQuery $query): string;

		/**
		 * compile simple insert command
		 *
		 * @param string $name
		 * @param array  $properties param => property
		 *
		 * @return string
		 */
		public function insert(string $name, array $properties): string;

		/**
		 * @param string $delimit
		 *
		 * @return string
		 */
		public function delimit(string $delimit): string;
	}
