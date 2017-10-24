<?php
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\IWhere;

		interface IUpdateQuery extends IQuery {
			/**
			 * update where
			 *
			 * @return IWhere
			 */
			public function where(): IWhere;
		}
