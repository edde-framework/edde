<?php
	declare(strict_types=1);
	namespace Edde;

	/**
	 * The very first object to be extended.
	 */
	class SimpleObject {
		public function __get(string $name) {
			throw new ObjectException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __set(string $name, $value) {
			throw new ObjectException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
