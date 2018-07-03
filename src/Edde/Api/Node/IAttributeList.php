<?php
	declare(strict_types=1);

	namespace Edde\Api\Node;

	use Edde\Api\Collection\IList;

	interface IAttributeList extends IList {
		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttributeList(string $name): bool;
	}
