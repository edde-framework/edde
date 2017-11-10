<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\Query\IUnlinkQuery;
		use Edde\Api\Schema\ILink;
		use Edde\Common\Object\Object;

		class UnlinkQuery extends Object implements IUnlinkQuery {
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
