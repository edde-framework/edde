<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Schema\ISchema;

		/**
		 * An Entity is extended Crate with some additional features.
		 */
		interface IEntity extends ICrate {
			/**
			 * get entity's schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * load the given data (they should be also filtered)
			 *
			 * @param array $source
			 *
			 * @return IEntity
			 */
			public function filter(array $source): IEntity;

			/**
			 * prepare m:n collection of related entities
			 *
			 * @param string $schema
			 * @param string $alias
			 *
			 * @return ICollection
			 */
			public function join(string $schema, string $alias): ICollection;

			/**
			 * is the entity loaded from storage, thus it exists?
			 *
			 * @param bool|null $exists set the flag or get the current state
			 *
			 * @return bool
			 */
			public function exists(bool $exists = null): bool;
		}
