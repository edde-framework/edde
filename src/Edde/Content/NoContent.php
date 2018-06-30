<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use ArrayIterator;

	class NoContent extends Content {
		public function __construct(string $type = 'text/plain') {
			parent::__construct(null, $type);
		}

		/** @inheritdoc */
		public function getIterator() {
			return new ArrayIterator([]);
		}
	}
