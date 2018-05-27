<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Query\IQuery;

	interface ICollectionManager {
		/**
		 * @param IQuery|null $query
		 *
		 * @return ICollection
		 */
		public function collection(IQuery $query = null): ICollection;
	}
