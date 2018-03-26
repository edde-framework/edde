<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;
	use Edde\Schema\ILink;

	class LinkQuery extends UnlinkQuery {
		/** @var IEntity */
		protected $to;

		public function __construct(IEntity $entity, ILink $link, IEntity $to) {
			parent::__construct($entity, $link);
			$this->to = $to;
		}

		/** @inheritdoc */
		public function getTo(): IEntity {
			return $this->to;
		}
	}
