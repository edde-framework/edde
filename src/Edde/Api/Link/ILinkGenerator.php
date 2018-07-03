<?php
	declare(strict_types = 1);

	namespace Edde\Api\Link;

	/**
	 * Particular link generator; it is some sort of oposite side of router.
	 */
	interface ILinkGenerator {
		/**
		 * generate output link
		 *
		 * @param mixed $generate
		 * @param array ...$parameterList
		 *
		 * @return string|null if null is returned, next generator will be used
		 */
		public function link($generate, ...$parameterList);
	}
