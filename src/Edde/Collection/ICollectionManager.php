<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	interface ICollectionManager {
		/**
		 * @return ICollection
		 */
		public function collection(): ICollection;
	}
