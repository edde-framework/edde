<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Query\SelectQuery;
	use Edde\Service\Container\Container;
	use Throwable;

	class CollectionManager extends Edde implements ICollectionManager {
		use Container;

		/** @inheritdoc */
		public function collection(): ICollection {
			try {
				return $this->container->create(Collection::class, [new SelectQuery()], __METHOD__);
			} catch (Throwable $exception) {
				throw new CollectionException(sprintf('Cannot create collection: %s', $exception->getMessage()), 0, $exception);
			}
		}
	}
