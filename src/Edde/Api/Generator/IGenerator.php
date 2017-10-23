<?php
	namespace Edde\Api\Generator;

	/**
	 * Simple value generator; originally a bit proprietary implementation for
	 * Schema package.
	 */
		interface IGenerator {
			/**
			 * generate a value (for example it could be obvious uuid or even last if from database)
			 *
			 * @param array $options
			 *
			 * @return mixed
			 */
			public function generate(array $options = []);
		}
