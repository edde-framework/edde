<?php
	namespace Edde\Api\Storage;

		interface ICollection extends \IteratorAggregate {
			/**
			 * @return \Traversable|IEntity[]
			 */
			public function getIterator();
		}
