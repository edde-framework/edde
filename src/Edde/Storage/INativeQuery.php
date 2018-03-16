<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Query\IQuery;

	interface INativeQuery extends IQuery {
		/**
		 * @return string
		 */
		public function getQuery(): string;

		/**
		 * @return array
		 */
		public function getParams(): array;
	}
