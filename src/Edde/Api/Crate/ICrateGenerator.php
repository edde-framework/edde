<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Schema\ISchema;

	interface ICrateGenerator extends IDeffered {
		/**
		 * generate source class (php source code) for the given schema
		 *
		 * @param ISchema $schema
		 *
		 * @return string[] array of crates with dependencies (key is crate FQN)
		 */
		public function compile(ISchema $schema): array;

		/**
		 * @param bool $force
		 *
		 * @return ICrateGenerator
		 */
		public function generate(bool $force = false): ICrateGenerator;

		/**
		 * include crate sources/it's autoloader
		 *
		 * @return ICrateGenerator
		 */
		public function include (): ICrateGenerator;
	}
