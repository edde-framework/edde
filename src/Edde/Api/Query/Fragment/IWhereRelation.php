<?php
	namespace Edde\Api\Query\Fragment;

		interface IWhereRelation extends IFragment {
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

			/**
			 * jump from group if there is one
			 *
			 * @return IWhereRelation
			 */
			public function end(): IWhereRelation;
		}
