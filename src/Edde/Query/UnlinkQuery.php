<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;
	use Edde\Object;
	use Edde\Schema\ILink;

	class UnlinkQuery extends Object implements IUnlinkQuery {
		/** @var IEntity */
		protected $entity;
		/** @var ILink */
		protected $link;

		public function __construct(IEntity $entity, ILink $link) {
			$this->entity = $entity;
			$this->link = $link;
		}

		/** @inheritdoc */
		public function getEntity(): IEntity {
			return $this->entity;
		}

		/** @inheritdoc */
		public function getLink(): ILink {
			return $this->link;
		}
	}
