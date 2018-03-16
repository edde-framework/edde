<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Schema\Exception\SchemaException;
	use Edde\Api\Schema\Exception\UnknownSchemaException;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Exception\StorageException;
	use Edde\Exception\Driver\DriverException;

	interface IEntityManager extends IConfigurable {
		/**
		 * just create an entity with the given schema
		 *
		 * @param ISchema $schema
		 *
		 * @return IEntity
		 */
		public function createEntity(ISchema $schema): IEntity;

		/**
		 * creates an entity; the set source (data) could make entity dirty
		 *
		 * @param string $schema
		 * @param array  $source
		 *
		 * @return IEntity
		 * @throws UnknownSchemaException
		 * @throws SchemaException
		 */
		public function create(string $schema, array $source = []): IEntity;

		/**
		 * when this method is used, entity would be marked as "existing"; that
		 * means on save there will be update action instead of insert
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return IEntity
		 */
		public function load(ISchema $schema, array $source): IEntity;

		/**
		 * create a collection of the given schema
		 *
		 * @param string $alias
		 * @param string $schema
		 *
		 * @return ICollection
		 * @throws UnknownSchemaException
		 * @throws SchemaException
		 */
		public function collection(string $alias, string $schema): ICollection;

		/**
		 * execute queued changes
		 *
		 * @param IEntityQueue $entityQueue
		 *
		 * @return IEntityManager
		 *
		 * @throws StorageException
		 * @throws DriverException
		 */
		public function execute(IEntityQueue $entityQueue): IEntityManager;
	}
