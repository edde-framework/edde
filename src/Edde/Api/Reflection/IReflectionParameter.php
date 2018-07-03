<?php
	declare(strict_types=1);

	namespace Edde\Api\Reflection;

	/**
	 * Reflection parameter which could be cached.
	 */
	interface IReflectionParameter {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return class name of a parameter (if any)
		 *
		 * @return string
		 */
		public function getClass();

		/**
		 * @return bool
		 */
		public function isOptional(): bool;
	}
