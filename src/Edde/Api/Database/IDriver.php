<?php
	namespace Edde\Api\Database;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Database\Exception\NativeQueryException;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;

		/**
		 * Database driver implementation.
		 */
		interface IDriver extends IConfigurable {
			/**
			 * translates and executes the given query
			 *
			 * @param IQuery $query
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 */
			public function execute(IQuery $query);

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
			 * executes native query on this engine
			 *
			 * @param INativeQuery $nativeQuery
			 *
			 * @return mixed
			 *
			 * @throws DriverQueryException
			 */
			public function native(INativeQuery $nativeQuery);

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
