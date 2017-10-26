<?php
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;

		interface IQueryBuilder extends IConfigurable {
			/**
			 * creates a native query from the given query
			 *
			 * @param IQuery $query
			 *
			 * @return INativeQuery
			 */
			public function build(IQuery $query): INativeQuery;

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
