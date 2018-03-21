<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Collection\ICollection;
	use Edde\Config\IConfigurable;
	use Edde\Schema\ISchema;

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
		 */
		public function create(string $schema, array $source = []): IEntity;

		/**
		 * shorthand for put & save
		 *
		 * @param string $schema
		 * @param array  $source
		 *
		 * @return IEntity
		 */
		public function save(string $schema, array $source): IEntity;

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
		 */
		public function collection(string $alias, string $schema): ICollection;

		/**
		 * execute queued changes
		 *
		 * @param IEntityQueue $entityQueue
		 *
		 * @return IEntityManager
		 */
		public function execute(IEntityQueue $entityQueue): IEntityManager;
	}
