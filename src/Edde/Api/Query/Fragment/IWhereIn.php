<?php
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Query\ISelectQuery;

		interface IWhereIn extends IFragment {
			/**
			 * where in subselect
			 *
			 * @param ISelectQuery $selectQuery
			 *
			 * @return IWhereRelation
			 */
			public function select(ISelectQuery $selectQuery): IWhereRelation;
		}
