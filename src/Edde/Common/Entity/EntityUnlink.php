<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityUnlink;
		use Edde\Api\Schema\ILink;
		use Edde\Common\Object\Object;

		class EntityUnlink extends Object implements IEntityUnlink {
			/**
			 * @var IEntity
			 */
			protected $entity;
			/**
			 * @var ILink
			 */
			protected $link;

			public function __construct(IEntity $entity, ILink $link) {
				$this->entity = $entity;
				$this->link = $link;
			}

			/**
			 * @return IEntity
			 */
			public function getEntity(): IEntity {
				return $this->entity;
			}

			/**
			 * @return ILink
			 */
			public function getLink(): ILink {
				return $this->link;
			}
		}
