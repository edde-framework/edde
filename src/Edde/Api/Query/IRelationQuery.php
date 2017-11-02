<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Schema\IRelation;

		interface IRelationQuery extends IQuery {
			/**
			 * source piece of data (should be already present in the storage)
			 *
			 * @param array $source
			 *
			 * @return IRelationQuery
			 */
			public function from(array $source): IRelationQuery;

			/**
			 * target piece of data; this one also should be already present in the storage
			 *
			 * @param array $source
			 *
			 * @return IRelationQuery
			 */
			public function to(array $source): IRelationQuery;

			/**
			 * @return IRelation
			 */
			public function getRelation(): IRelation;

			/**
			 * get from source data
			 *
			 * @return array
			 */
			public function getFrom(): array;

			/**
			 * get to source data
			 *
			 * @return array
			 */
			public function getTo(): array;
		}
