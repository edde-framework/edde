<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Object;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use stdClass;
	use Throwable;

	class EntityManager extends Object implements IEntityManager {
		use SchemaManager;
		use Container;

		/** @inheritdoc */
		public function create(string $schema): IEntity {
			try {
				return $this->container->inject(new Entity($this->schemaManager->load($schema)));
			} catch (Throwable $exception) {
				throw new EntityException(sprintf('Cannot create requested entity [%s].', $schema), 0, $exception);
			}
		}

		/** @inheritdoc */
		public function save(string $schema, stdClass $source): IEntity {
			try {
				$entity = $this->create($schema);
				$entity->put($this->schemaManager->generate($entity->getSchema(), $source));
				$source = $this->schemaManager->generate($schema, $source);
				$this->schemaManager->validate($schema, $source);
				$this->connection->save($source, $schema);
				$entity->save();
				return $entity;
			} catch (Throwable $exception) {
				throw new EntityException(sprintf('Cannot insert item into schema [%s]: %s', $schema, $exception->getMessage()), 0, $exception);
			}
		}
	}
