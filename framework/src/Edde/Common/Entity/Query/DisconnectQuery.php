<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Query\IDisconnectQuery;
	use Edde\Api\Schema\IRelation;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Common\Storage\Query\AbstractQuery;
	use Edde\Common\Storage\Query\Fragment\WhereGroup;

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
