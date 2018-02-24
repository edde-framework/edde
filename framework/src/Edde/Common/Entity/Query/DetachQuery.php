<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Query\IDetachQuery;
	use Edde\Api\Schema\IRelation;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Common\Storage\Query\AbstractQuery;
	use Edde\Common\Storage\Query\Fragment\WhereGroup;

	class DetachQuery extends AbstractQuery implements IDetachQuery {
		/** @var IEntity */
		protected $entity;
		/** @var IEntity */
		protected $target;
		/** @var IRelation */
		protected $relation;
		/** @var IWhereGroup */
		protected $where;

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

		/** @inheritdoc */
		public function where(string $name, string $relation, $value): IDetachQuery {
			$this->getWhere()->and()->value($name, $relation, $value);
			return $this;
		}

		/** @inheritdoc */
		public function hasWhere(): bool {
			return $this->where !== null;
		}

		/** @inheritdoc */
		public function getWhere(): IWhereGroup {
			return $this->where ?: $this->where = new WhereGroup();
		}
	}
