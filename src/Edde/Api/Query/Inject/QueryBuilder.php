<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Inject;

		use Edde\Api\Query\IQueryBuilder;

		trait QueryBuilder {
			/**
			 * @var IQueryBuilder
			 */
			protected $queryBuilder;

			/**
			 * @param IQueryBuilder $queryBuilder
			 */
			public function lazyQueryBuilder(IQueryBuilder $queryBuilder) {
				$this->queryBuilder = $queryBuilder;
			}
		}
