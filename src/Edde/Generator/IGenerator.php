<?php
	declare(strict_types=1);
	namespace Edde\Generator;

	/**
	 * Simple value generator; originally a bit proprietary implementation for
	 * Schema package.
	 */
	interface IGenerator {
		/**
		 * generate a value (for example it could be obvious uuid or even last id from database)
		 *
		 * @param array $options
		 *
		 * @return mixed
		 */
		public function generate(array $options = []);
	}
