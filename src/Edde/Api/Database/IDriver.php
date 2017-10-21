<?php
	namespace Edde\Api\Database;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Database\Exception\NativeQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\IntegrityException;

		/**
		 * Database driver implementation based on PDO; if there should be another type of driver,
		 * like neo4j, use standalone Storage implementation.
		 */
		interface IDriver extends IConfigurable {
			/**
			 * executes native query on this engine
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function native(INativeQuery $nativeQuery): \PDOStatement;

			/**
			 * try to translate the given query to native query
			 *
			 * @param IQuery $query
			 *
			 * @return INativeQuery
			 *
			 * @throws NativeQueryException
			 */
			public function toNative(IQuery $query): INativeQuery;

			/**
			 * translates and executes the given query
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function execute(IQuery $query): \PDOStatement;

			/**
			 * delimite input string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function delimite(string $delimite): string;

			/**
			 * quote input string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function quote(string $delimite): string;

			/**
			 * translate input type to engine internal type
			 *
			 * @param string $type
			 *
			 * @return string
			 */
			public function type(string $type): string;
		}
