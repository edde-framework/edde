<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityLink;
		use Edde\Api\Schema\ILink;

		class EntityLink extends EntityUnlink implements IEntityLink {
			/**
			 * @var IEntity
			 */
			protected $to;

			public function __construct(IEntity $entity, ILink $link, IEntity $to) {
				parent::__construct($entity, $link);
				$this->to = $to;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): IEntity {
				return $this->to;
			}
		}
