<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;

		interface IStorage extends IConfigurable {
			/**
			 * execute the given query against a storage; query should be translated into native query and
			 * executed by a native() method
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 */
			public function execute(IQuery $query);

			/**
			 * executes native query on this storage
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 */
			public function native(INativeQuery $nativeQuery);
		}
