<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\IUpdateRelationQuery;

		/**
		 * General relation query (1:n, m:n).
		 */
		class UpdateRelationQuery extends CreateRelationQuery implements IUpdateRelationQuery {
		}
