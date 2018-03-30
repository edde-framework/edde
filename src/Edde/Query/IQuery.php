<?php
	declare(strict_types=1);
	namespace Edde\Query;

	interface IQuery {
		/**
		 * return query type
		 *
		 * @return string
		 */
		public function getType(): string;
	}
