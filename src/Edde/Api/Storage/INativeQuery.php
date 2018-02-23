<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

	use Edde\Api\Storage\Query\IQuery;

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
