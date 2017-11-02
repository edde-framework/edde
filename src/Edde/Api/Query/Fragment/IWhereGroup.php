<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereGroup extends IFragment {
			/**
			 * where and relation
			 *
			 * @return IWhere
			 */
			public function and (): IWhere;

			/**
			 * where or relation
			 *
			 * @return IWhere
			 */
			public function or (): IWhere;
		}
