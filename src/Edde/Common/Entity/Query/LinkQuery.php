<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Query\ILinkQuery;
	use Edde\Schema\ILink;

	class LinkQuery extends UnlinkQuery implements ILinkQuery {
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
