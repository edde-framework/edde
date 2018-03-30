<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface INative {
		/**
		 * @return string
		 */
		public function getQuery(): string;

		/**
		 * @return array
		 */
		public function getParams(): array;

		public function __toString(): string;
	}
