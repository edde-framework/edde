<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Query\IQuery;

		interface IStorage extends IConfigurable {
			/**
			 * start a transaction on the storage
			 *
			 * @param bool $exclusive
			 *
			 * @return IStorage
			 */
			public function start(bool $exclusive = false): IStorage;

			/**
			 * commit a transaction on storage
			 *
			 * @return IStorage
			 */
			public function commit(): IStorage;

			/**
			 * rollback a transaction on storage
			 *
			 * @return IStorage
			 */
			public function rollback(): IStorage;

			/**
			 * execute the given query; query will be translated into native query
			 *
			 * @param IQuery $query
			 *
			 * @return IStream
			 */
			public function execute(IQuery $query);

			/**
			 * prepare stream for the given query
			 *
			 * @param IQuery $query
			 *
			 * @return IStream
			 */
			public function stream(IQuery $query): IStream;

			/**
			 * execute the given native query and return stream as a result
			 *
			 * @param mixed $query
			 * @param array $parameterList
			 *
			 * @return IStream
			 */
			public function native($query, array $parameterList = []);
		}
