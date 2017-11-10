<?php
	namespace Edde\Common\Entity\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Query\IDeleteQuery;
		use Edde\Common\Storage\Query\AbstractQuery;

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
