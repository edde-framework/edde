<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity\Query;

	use Edde\Api\Entity\IEntityQueue;
	use Edde\Api\Entity\Query\IQueryQueue;
	use Edde\Common\Storage\Query\AbstractQuery;

	class QueryQueue extends AbstractQuery implements IQueryQueue {
		/** @var IEntityQueue */
		protected $entityQueue;

		public function __construct(IEntityQueue $entityQueue) {
			$this->entityQueue = $entityQueue;
		}

		/** @inheritdoc */
		public function getEntityQueue(): IEntityQueue {
			return $this->entityQueue;
		}
	}
