<?php
	declare(strict_types=1);

	namespace Edde\Api\Callback;

	/**
	 * Callback's parameter.
	 */
	interface IParameter {
		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @return bool
		 */
		public function hasClass();

		/**
		 * @return string|null
		 */
		public function getClass();

		/**
		 * @return bool
		 */
		public function isOptional();
	}
