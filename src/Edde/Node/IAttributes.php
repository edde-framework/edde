<?php
	declare(strict_types=1);
	namespace Edde\Node;

	use Edde\Collection\IHashMap;

	interface IAttributes extends IHashMap {
		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttributes(string $name): bool;
	}
