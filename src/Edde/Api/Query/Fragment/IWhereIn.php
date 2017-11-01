<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Query\ISelectQuery;

		interface IWhereIn extends IFragment {
			/**
			 * where in sub select
			 *
			 * @param ISelectQuery $selectQuery
			 *
			 * @return IWhereRelation
			 */
			public function select(ISelectQuery $selectQuery): IWhereRelation;
		}
