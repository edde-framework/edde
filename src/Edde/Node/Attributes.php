<?php
	declare(strict_types=1);
	namespace Edde\Node;

	use Edde\Collection\AbstractList;

	class Attributes extends AbstractList implements IAttributes {
		/**
		 * @inheritdoc
		 */
		public function hasAttributes(string $name): bool {
			return isset($this->list[$name]) && $this->list[$name] instanceof IAttributes;
		}
	}
