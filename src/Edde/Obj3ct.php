<?php
	declare(strict_types=1);
	namespace Edde;

	class Obj3ct {
		public function __get(string $name) {
			throw new Obj3ctException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __set(string $name, $value) {
			throw new Obj3ctException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		/**
		 * because PHP has some cool shit things like it cannot call
		 * magic parent, this method helps to standardize parent::__clone calls
		 * around the code
		 */
		public function __clone() {
		}
	}
