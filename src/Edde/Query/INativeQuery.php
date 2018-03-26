<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface INativeQuery extends IQuery {
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
		public function toString(): string;
	}
