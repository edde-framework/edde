<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\EntityQueue;

	class QueryQueue extends AbstractQuery {
		/** @var EntityQueue */
		protected $entityQueue;

		public function __construct(EntityQueue $entityQueue) {
			$this->entityQueue = $entityQueue;
		}

		/** @inheritdoc */
		public function getEntityQueue(): EntityQueue {
			return $this->entityQueue;
		}
	}
