<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use stdClass;

	class EntityManager extends Edde implements IEntityManager {
		use Container;
		use SchemaManager;

		/** @inheritdoc */
		public function entity(string $schema, stdClass $default = null): IEntity {
			/** @var $entity IEntity */
			$entity = $this->container->create(Entity::class, [$this->schemaManager->getSchema($schema)], __METHOD__);
			if ($default) {
				$entity->push($default);
			}
			return $entity;
		}
	}
