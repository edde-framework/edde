<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use ArrayAccess;

	interface IEntity extends ArrayAccess {
		/**
		 * return schema name of this entity
		 *
		 * @return string
		 */
		public function getSchema(): string;

		/**
		 * return internal source
		 *
		 * @return array
		 */
		public function toArray(): array;
	}
