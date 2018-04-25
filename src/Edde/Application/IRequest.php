<?php
	declare(strict_types=1);
	namespace Edde\Application;

	interface IRequest {
		/**
		 * get requested class
		 *
		 * @return string
		 */
		public function getService(): string;

		/**
		 * get requested method
		 *
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * parameters should be passed to requested method
		 *
		 * @return array
		 */
		public function getParams(): array;
	}
