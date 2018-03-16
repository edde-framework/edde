<?php
	declare(strict_types=1);
	namespace Edde\Node;

	use Edde\Collection\IList;

	interface IAttributes extends IList {
		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttributes(string $name): bool;
	}
