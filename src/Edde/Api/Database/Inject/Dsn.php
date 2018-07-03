<?php
	declare(strict_types=1);

	namespace Edde\Api\Database\Inject;

	use Edde\Api\Database\IDsn;

	trait Dsn {
		/**
		 * @var IDsn
		 */
		protected $dsn;

		public function lazyDsn(IDsn $dsn) {
			$this->dsn = $dsn;
		}
	}
