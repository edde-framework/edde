<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Connection\IConnection;
	use Edde\Object;

	class Collection extends Object implements ICollection {
		/** @var IConnection */
		protected $connection;

		/**
		 * @param IConnection $connection
		 */
		public function __construct(IConnection $connection) {
			$this->connection = $connection;
		}

		/** @inheritdoc */
		public function getIterator() {
			throw new \Exception('not implemented yet');
		}
	}
