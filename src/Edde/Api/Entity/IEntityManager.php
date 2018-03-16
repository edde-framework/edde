<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Schema\ISchema;
	use Edde\Exception\Driver\DriverException;
	use Edde\Exception\Schema\UnknownSchemaException;
	use Edde\Exception\Storage\StorageException;

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
		 * @throws \Edde\Exception\Schema\SchemaException
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
		 * @throws \Edde\Exception\Schema\UnknownSchemaException
		 * @throws \Edde\Exception\Schema\SchemaException
		 */
		public function collection(string $alias, string $schema): ICollection;

		/**
		 * execute queued changes
		 *
		 * @param IEntityQueue $entityQueue
		 *
		 * @return IEntityManager
		 *
		 * @throws \Edde\Exception\Storage\StorageException
		 * @throws DriverException
		 */
		public function execute(IEntityQueue $entityQueue): IEntityManager;
	}
