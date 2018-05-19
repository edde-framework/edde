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
			return ($entity = new Entity($this->schemaManager->getSchema($schema))) && $default ? $entity->push($default) : $entity;
		}
	}
