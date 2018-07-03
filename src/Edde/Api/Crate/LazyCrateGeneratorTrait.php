<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * Lazy dependency on a crate generator.
	 */
	trait LazyCrateGeneratorTrait {
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;

		/**
		 * @param ICrateGenerator $crateGenerator
		 */
		public function lazyCrateGenerator(ICrateGenerator $crateGenerator) {
			$this->crateGenerator = $crateGenerator;
		}
	}
