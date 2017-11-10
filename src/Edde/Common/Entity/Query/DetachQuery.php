<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Query\IDetachQuery;
		use Edde\Api\Schema\IRelation;
		use Edde\Common\Storage\Query\AbstractQuery;

		class DetachQuery extends AbstractQuery implements IDetachQuery {
			/** @var IEntity */
			protected $entity;
			/** @var IEntity */
			protected $target;
			/** @var IRelation */
			protected $relation;

			public function __construct(IEntity $entity, IEntity $target, IRelation $relation) {
				$this->entity = $entity;
				$this->target = $target;
				$this->relation = $relation;
			}

			/** @inheritdoc */
			public function getEntity(): IEntity {
				return $this->entity;
			}

			/** @inheritdoc */
			public function getTarget(): IEntity {
				return $this->target;
			}

			/** @inheritdoc */
			public function getRelation(): IRelation {
				return $this->relation;
			}
		}
