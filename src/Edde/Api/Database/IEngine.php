<?php
	namespace Edde\Api\Database;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;

		/**
		 * Database engine detail implementation.
		 */
		interface IEngine extends IConfigurable {
			/**
			 * translates and executes the given query
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 */
			public function execute(IQuery $query);

			/**
			 * executes native query on this engine
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 */
			public function native(INativeQuery $nativeQuery);
		}
