<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;

		interface IQueryBuilder extends IConfigurable {
			/**
			 * @param IQuery $query
			 *
			 * @return INativeTransaction
			 */
			public function query(IQuery $query) : INativeTransaction;

			/**
			 * delimite the given string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function delimite(string $delimite) : string;

			/**
			 * translate input type to internal type
			 *
			 * @param string $type
			 *
			 * @return string
			 */
			public function type(string $type) : string;
		}
