<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Object;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Throwable;

	class EntityManager extends Object implements IEntityManager {
		use SchemaManager;
		use Container;

		/** @inheritdoc */
		public function entity(string $schema): IEntity {
			try {
				return $this->container->inject(new Entity($this->schemaManager->load($schema)));
			} catch (Throwable $exception) {
				throw new EntityException(sprintf('Cannot create requested entity [%s].', $schema), 0, $exception);
			}
		}
	}
