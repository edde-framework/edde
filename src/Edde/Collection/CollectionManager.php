<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\SelectQuery;
	use Edde\Service\Container\Container;

	class CollectionManager extends Edde implements ICollectionManager {
		use Container;

		/** @inheritdoc */
		public function collection(): ICollection {
			return $this->container->create(Collection::class, [new SelectQuery()], __METHOD__);
		}
	}
