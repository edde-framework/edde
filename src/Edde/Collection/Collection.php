<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Object;

	class Collection extends Object implements ICollection {
		/** @inheritdoc */
		public function getIterator() {
			throw new \Exception('not implemented yet');
		}
	}
