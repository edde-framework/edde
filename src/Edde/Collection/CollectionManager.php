<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Object;
	use Edde\Service\Container\Container;
	use Throwable;

	class CollectionManager extends Object implements ICollectionManager {
		use Container;

		/** @inheritdoc */
		public function collection(): ICollection {
			try {
				return $this->container->create(Collection::class);
			} catch (Throwable $exception) {
				throw new CollectionException(sprintf('Cannot create collection: %s', $exception->getMessage()), 0, $exception);
			}
		}
	}
