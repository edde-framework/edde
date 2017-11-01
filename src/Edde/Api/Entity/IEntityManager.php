<?php
	namespace Edde\Api\Entity;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Schema\ISchema;

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
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return IEntity
			 */
			public function factory(string $schema, array $source): IEntity;
		}
