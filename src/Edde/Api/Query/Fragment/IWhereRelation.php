<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereRelation extends IFragment {
			/**
			 * where and relation
			 *
			 * @return IWhereFragment
			 */
			public function and (): IWhereFragment;

			/**
			 * where or relation
			 *
			 * @return IWhereFragment
			 */
			public function or (): IWhereFragment;

			/**
			 * jump from group if there is one
			 *
			 * @return IWhereRelation
			 */
			public function end(): IWhereRelation;
		}
