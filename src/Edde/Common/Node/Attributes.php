<?php
	declare(strict_types=1);
	namespace Edde\Common\Node;

	use Edde\Collection\AbstractList;

	class Attributes extends AbstractList implements \Edde\Node\IAttributes {
		/**
		 * @inheritdoc
		 */
		public function hasAttributes(string $name): bool {
			return isset($this->list[$name]) && $this->list[$name] instanceof \Edde\Node\IAttributes;
		}
	}
