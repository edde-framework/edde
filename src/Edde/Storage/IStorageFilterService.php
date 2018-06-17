<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Filter\FilterException;
	use Edde\Query\IParam;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use Edde\Validator\ValidatorException;

	interface IStorageFilterService extends IConfigurable {
		/**
		 * filter input (insert) by input filters (internally could use proprietary filtering algorithm)
		 *
		 * @param ISchema $schema schema of an input
		 * @param array   $input
		 *
		 * @return array
		 *
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function input(ISchema $schema, array $input): array;

		/**
		 * prepare for update; the algorithm could slightly differ from an input
		 *
		 * @param ISchema $schema
		 * @param array   $update
		 *
		 * @return array
		 *
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function update(ISchema $schema, array $update): array;

		/**
		 * prepare an output
		 *
		 * @param ISchema $schema
		 * @param array   $output
		 *
		 * @return array
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		public function output(ISchema $schema, array $output): array;

		/**
		 * filter input params for a storage
		 *
		 * @param IQuery $query
		 * @param array  $binds
		 *
		 * @return IParam[]
		 *
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function params(IQuery $query, array $binds = []): array;
	}
