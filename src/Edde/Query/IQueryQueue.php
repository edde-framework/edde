<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntityQueue;

	interface IQueryQueue extends IQuery {
		/**
		 * @return IEntityQueue
		 */
		public function getEntityQueue(): IEntityQueue;
	}
