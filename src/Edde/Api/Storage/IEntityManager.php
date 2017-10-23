<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Crate\IProperty;
		use Edde\Api\Filter\IFilter;
		use Edde\Api\Storage\Exception\UnknownGeneratorException;

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
			 * quite complex method: create an entity, fill data (entity is NOT dirty) and
			 * try to convert properties due it's types (for example database will not return
			 * float as float, but as a string, ...)
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return IEntity
			 */
			public function factory(string $schema, array $source): IEntity;

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

			/**
			 * check properties with a generator and try to generate value for them
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntityManager
			 *
			 * @throws UnknownGeneratorException
			 */
			public function generate(IEntity $entity): IEntityManager;
		}
