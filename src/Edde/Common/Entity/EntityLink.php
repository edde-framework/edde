<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityLink;
		use Edde\Api\Schema\ILink;
		use Edde\Common\Object\Object;

		class EntityLink extends Object implements IEntityLink {
			/**
			 * @var IEntity
			 */
			protected $from;
			/**
			 * @var IEntity
			 */
			protected $to;
			/**
			 * @var ILink
			 */
			protected $link;

			public function __construct(IEntity $from, IEntity $to, ILink $link) {
				$this->from = $from;
				$this->to = $to;
				$this->link = $link;
			}

			/**
			 * @inheritdoc
			 */
			public function getFrom(): IEntity {
				return $this->from;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): IEntity {
				return $this->to;
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(): ILink {
				return $this->link;
			}
		}
