<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	interface ICollectionManager {
		/**
		 * @return ICollection
		 *
		 * @throws CollectionException
		 */
		public function collection(): ICollection;
	}
