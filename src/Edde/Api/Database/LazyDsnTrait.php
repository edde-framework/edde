<?php
	declare(strict_types=1);

	namespace Edde\Api\Database;

	trait LazyDsnTrait {
		/**
		 * @var IDsn
		 */
		protected $dsn;

		public function lazyDsn(IDsn $dsn) {
			$this->dsn = $dsn;
		}
	}
