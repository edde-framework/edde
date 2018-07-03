<?php
	declare(strict_types=1);

	namespace Edde\Api\Link;

	/**
	 * Explicit interface for a link source.
	 */
	interface ILink {
		/**
		 * source for a link generator
		 *
		 * @return mixed
		 */
		public function getLink();

		/**
		 * optional parameter list; can be used for url generation or target link generator configuration
		 *
		 * @return array
		 */
		public function getParameterList(): array;
	}
