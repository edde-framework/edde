<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Object;
	use Edde\Schema\ISchema;
	use Edde\Service\Container\Container;
	use Throwable;

	class EntityManager extends Object implements IEntityManager {
		use Container;

		/** @inheritdoc */
		public function entity(ISchema $schema): IEntity {
			try {
				return $this->container->inject(new Entity($schema));
			} catch (Throwable $exception) {
				throw new EntityException(sprintf('Cannot create requested entity [%s]: %s', $schema->getName(), $exception->getMessage()), 0, $exception);
			}
		}
	}
