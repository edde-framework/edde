<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Filter\FilterException;
	use Edde\Schema\SchemaException;
	use Edde\Validator\ValidatorException;

	interface IHydrator {
		/**
		 * hydrate the given input (row, record) to (arbitrary) output
		 *
		 * @param array $source
		 *
		 * @return mixed
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function hydrate(array $source);

		/**
		 * hydrate input (from php side to storage)
		 *
		 * @param string $name
		 * @param array  $input
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function input(string $name, array $input): array;

		/**
		 * hydrate data for update (from php side to storage); this method should respect
		 * for example unset uuid (even it make no sense) as there could be generator
		 * bound to a value
		 *
		 * @param string $name
		 * @param array  $update
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		public function update(string $name, array $update): array;

		/**
		 * hydrate output (from storage to php side)
		 *
		 * @param string $name
		 * @param array  $output
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function output(string $name, array $output): array;
	}
