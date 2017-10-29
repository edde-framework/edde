<?php
	namespace Edde\Api\Query;

		interface IInsertQuery extends IQuery {
			/**
			 * set an alias to insert query table
			 *
			 * @param string $alias
			 *
			 * @return IInsertQuery
			 */
			public function alias(string $alias): IInsertQuery;
		}
