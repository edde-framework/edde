<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Common\Storage\Query\AbstractQuery;
	use Edde\Entity\IEntity;

	class DeleteQuery extends AbstractQuery implements IDeleteQuery {
		/** @var IEntity */
		protected $entity;

		public function __construct(IEntity $entity) {
			$this->entity = $entity;
		}

		/** @inheritdoc */
		public function getEntity(): IEntity {
			return $this->entity;
		}
	}
