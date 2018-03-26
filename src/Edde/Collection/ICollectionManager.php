<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Config\IConfigurable;

	interface ICollectionManager extends IConfigurable {
		/**
		 * @return ICollection
		 *
		 * @throws CollectionException
		 */
		public function collection(): ICollection;
	}
