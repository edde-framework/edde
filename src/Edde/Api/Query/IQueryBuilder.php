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
