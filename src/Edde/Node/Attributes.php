<?php
	declare(strict_types=1);
	namespace Edde\Node;

	use Edde\Collection\HashMap;

	class Attributes extends HashMap implements IAttributes {
		/** @inheritdoc */
		public function hasAttributes(string $name): bool {
			return isset($this->hashMap[$name]) && $this->hashMap[$name] instanceof IAttributes;
		}
	}
