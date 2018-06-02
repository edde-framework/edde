<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Config\IConfigurable;
	use Edde\Filter\FilterException;
	use Edde\Schema\ISchema;
	use Edde\Validator\ValidatorException;
	use stdClass;

	interface IStorageFilterService extends IConfigurable {
		/**
		 * filter input (insert) by input filters (internally could use proprietary filtering algorithm)
		 *
		 * @param ISchema  $schema schema of an input
		 * @param stdClass $input  input should be cloned to prevent side effects
		 *
		 * @return stdClass
		 *
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function input(ISchema $schema, stdClass $input): stdClass;

		/**
		 * prepare for update; the algorithm could slightly differ from an input
		 *
		 * @param ISchema  $schema
		 * @param stdClass $update
		 *
		 * @return stdClass
		 *
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function update(ISchema $schema, stdClass $update): stdClass;

		/**
		 * prepare an output
		 *
		 * @param ISchema  $schema
		 * @param stdClass $output
		 *
		 * @return stdClass
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		public function output(ISchema $schema, stdClass $output): stdClass;
	}
