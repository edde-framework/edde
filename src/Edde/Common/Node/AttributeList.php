<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAttributeList;
	use Edde\Common\Collection\AbstractList;

	class AttributeList extends AbstractList implements IAttributeList {
		/**
		 * @inheritdoc
		 */
		public function hasAttributeList(string $name): bool {
			return isset($this->list[$name]) && $this->list[$name] instanceof IAttributeList;
		}
	}
