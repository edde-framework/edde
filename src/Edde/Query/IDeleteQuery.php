<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Entity\IEntity;

	interface IDeleteQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;
	}
