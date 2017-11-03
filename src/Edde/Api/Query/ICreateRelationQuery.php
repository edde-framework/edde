<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Schema\IRelation;

		interface ICreateRelationQuery extends IQuery {
			/**
			 * has this relation some properties?
			 *
			 * @return bool
			 */
			public function hasSource(): bool;

			/**
			 * return properties of this relation
			 *
			 * @return array
			 */
			public function getSource(): array;

			/**
			 * source piece of data (should be already present in the storage)
			 *
			 * @param array $source
			 *
			 * @return ICreateRelationQuery
			 */
			public function from(array $source): ICreateRelationQuery;

			/**
			 * target piece of data; this one also should be already present in the storage
			 *
			 * @param array $source
			 *
			 * @return ICreateRelationQuery
			 */
			public function to(array $source): ICreateRelationQuery;

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
