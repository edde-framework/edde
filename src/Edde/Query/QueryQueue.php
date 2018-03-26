<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntityQueue;

	class QueryQueue extends AbstractQuery {
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
