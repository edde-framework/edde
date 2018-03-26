<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\ICrate;

	/**
	 * An Entity is extended Crate with some additional features.
	 */
	interface IEntity extends ICrate {
		/**
		 * persist changes marked in this entity (update, delete, ...)
		 *
		 * @return IEntity
		 */
		public function commit(): ICrate;
	}
