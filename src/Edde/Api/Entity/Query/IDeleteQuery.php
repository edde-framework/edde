<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Storage\Query\IQuery;

	interface IDeleteQuery extends IQuery {
		/**
		 * @return IEntity
		 */
		public function getEntity(): IEntity;
	}
