<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface INativeQuery {
		/**
		 * @return string
		 */
		public function getQuery(): string;

		/**
		 * @return array
		 */
		public function getParams(): array;

		/**
		 * @return string
		 */
		public function __toString(): string;
	}
