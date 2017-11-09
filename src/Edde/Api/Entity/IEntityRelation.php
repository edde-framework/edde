<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Schema\IRelation;

		interface IEntityRelation {
			/**
			 * @return IEntity
			 */
			public function getEntity(): IEntity;

			/**
			 * @return IEntity
			 */
			public function getTarget(): IEntity;

			/**
			 * @return IEntity
			 */
			public function getUsing(): IEntity;

			/**
			 * @return IRelation
			 */
			public function getRelation(): IRelation;
		}
