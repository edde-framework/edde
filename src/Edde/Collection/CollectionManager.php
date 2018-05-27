<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\IQuery;
	use Edde\Query\Query;
	use Edde\Service\Container\Container;

	class CollectionManager extends Edde implements ICollectionManager {
		use Container;

		/** @inheritdoc */
		public function collection(IQuery $query = null): ICollection {
			return $this->container->create(Collection::class, [$query ?: new Query()], __METHOD__);
		}
	}
