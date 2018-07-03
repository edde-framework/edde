<?php
	declare(strict_types=1);

	namespace Edde\Api\Link;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Particular link generator; it is some sort of oposite side of router.
	 */
	interface ILinkGenerator extends IConfigurable {
		/**
		 * generate output link
		 *
		 * @param mixed $generate
		 * @param array $parameterList
		 *
		 * @return null|string if null is returned, next generator will be used
		 */
		public function link($generate, array $parameterList = []);
	}
