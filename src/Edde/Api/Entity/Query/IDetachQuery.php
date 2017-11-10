<?php
	namespace Edde\Api\Entity\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Schema\IRelation;
		use Edde\Api\Storage\Query\IQuery;

		interface IDetachQuery extends IQuery {
			/**
			 * @return IEntity
			 */
			public function getEntity(): IEntity;

			/**
			 * @return IEntity
			 */
			public function getTarget(): IEntity;

			/**
			 * @return IRelation
			 */
			public function getRelation(): IRelation;
		}
