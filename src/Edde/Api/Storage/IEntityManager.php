<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Crate\IProperty;
		use Edde\Api\Filter\IFilter;

		interface IEntityManager extends IConfigurable {
			/**
			 * register the given filter as a named generator
			 *
			 * @param string  $name
			 * @param IFilter $filter
			 *
			 * @return IEntityManager
			 */
			public function registerGenerator(string $name, IFilter $filter): IEntityManager;

			/**
			 * register list of filters (generators)
			 *
			 * @param IFilter[] $filterList
			 *
			 * @return IEntityManager
			 */
			public function registerGeneratorList(array $filterList): IEntityManager;

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
			 * entity is using schema with types, so entity manager should do accurate
			 * dirty state detection (implementation in crate is quite weak)
			 *
			 * @param IEntity $entity
			 *
			 * @return bool
			 */
			public function isDirty(IEntity $entity): bool;

			/**
			 * return dirty properties of the given entity
			 *
			 * @param IEntity $entity
			 *
			 * @return IProperty[]
			 */
			public function getDirtyProperties(IEntity $entity): array;
		}
