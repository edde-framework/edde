<?php
	declare(strict_types = 1);

	namespace Edde\Api\Database;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Query\IStaticQuery;
	use PDOStatement;

	/**
	 * Custom driver per database engine.
	 */
	interface IDriver extends IDeffered {
		/**
		 * start a transaction
		 *
		 * @param bool $exclusive if true and there is already transaction, exception should be thrown
		 *
		 * @return $this
		 */
		public function start(bool $exclusive = false);

		/**
		 * commit a transaciton
		 *
		 * @return $this
		 */
		public function commit();

		/**
		 * rollback a transaction
		 *
		 * @return $this
		 */
		public function rollback();

		/**
		 * @param string $delimite
		 *
		 * @return string
		 */
		public function delimite(string $delimite): string;

		/**
		 * @param string $quote
		 *
		 * @return string
		 */
		public function quote(string $quote): string;

		/**
		 * translate common (php) type to the database type (e.g. bool to int, ...)
		 *
		 * @param string $type
		 *
		 * @return string
		 */
		public function type(string $type): string;

		/**
		 * @param IQuery $query
		 *
		 * @return PDOStatement
		 */
		public function execute(IQuery $query): PDOStatement;

		/**
		 * execute native query
		 *
		 * @param IStaticQuery $staticQuery
		 *
		 * @return mixed
		 */
		public function native(IStaticQuery $staticQuery);
	}
