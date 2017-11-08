<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityRelation;
		use Edde\Api\Schema\IRelation;
		use Edde\Common\Object\Object;

		class EntityRelation extends Object implements IEntityRelation {
			/**
			 * @var IEntity
			 */
			protected $entity;
			/**
			 * @var IEntity
			 */
			protected $target;
			/**
			 * @var IEntity
			 */
			protected $using;
			/**
			 * @var IRelation
			 */
			protected $relation;

			public function __construct(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation) {
				$this->entity = $entity;
				$this->target = $target;
				$this->using = $using;
				$this->relation = $relation;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntity(): IEntity {
				return $this->entity;
			}

			/**
			 * @inheritdoc
			 */
			public function getTarget(): IEntity {
				return $this->target;
			}

			/**
			 * @inheritdoc
			 */
			public function getUsing(): IEntity {
				return $this->using;
			}

			/**
			 * @inheritdoc
			 */
			public function getRelation(): IRelation {
				return $this->relation;
			}
		}
