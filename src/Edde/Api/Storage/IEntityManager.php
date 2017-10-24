<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;

		interface IEntityManager extends IConfigurable {
			/**
			 * just create an entity with the given schema
			 *
			 * @param string $schema
			 *
			 * @return IEntity
			 */
			public function createEntity(string $schema): IEntity;

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
		}
