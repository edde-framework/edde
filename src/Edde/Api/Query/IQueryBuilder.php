<?php
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;

		interface IQueryBuilder extends IConfigurable {
			/**
			 * takes an IQL input and convert it to native query; there could
			 * be more queries per one IQL
			 *
			 * @param IQuery $query
			 *
			 * @return INativeBatch
			 */
			public function build(IQuery $query): INativeBatch;

			/**
			 * delimite the given string
			 *
			 * @param string $delimite
			 *
			 * @return string
			 */
			public function delimite(string $delimite): string;

			/**
			 * translate input type to internal type
			 *
			 * @param string $type
			 *
			 * @return string
			 */
			public function type(string $type): string;
		}
