<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;
	use Edde\Query\Fragment\IWhereGroup;
	use Edde\Query\Fragment\WhereGroup;
	use Edde\Schema\IRelation;

	class DisconnectQuery extends AbstractQuery implements IDisconnectQuery {
		/** @var IEntity */
		protected $entity;
		/** @var IRelation */
		protected $relation;
		/** @var IWhereGroup */
		protected $where;

		public function __construct(IEntity $entity, IRelation $relation) {
			$this->entity = $entity;
			$this->relation = $relation;
		}

		/** @inheritdoc */
		public function getEntity(): IEntity {
			return $this->entity;
		}

		/** @inheritdoc */
		public function getRelation(): IRelation {
			return $this->relation;
		}

		/** @inheritdoc */
		public function where(string $name, string $relation, $value): IDisconnectQuery {
			$this->getWhere()->and()->expression($name, $relation, $value);
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
