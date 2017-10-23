<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Config\IConfigurable;

		interface IEntityManager extends IConfigurable {
			/**
			 * creates an entity; the set source (data) could make entity dirty
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return IEntity
			 */
			public function create(string $schema, array $source = []): IEntity;
		}
