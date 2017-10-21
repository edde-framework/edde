<?php
	namespace Edde\Api\Crate;

	/**
	 * Crate is very simple implementation of object able to track it's changes and do some common stuff
	 * usually ORM objects do, but in abstract way. Crate with a Schema is an Entity.
	 */
		interface ICrate {
			/**
			 * is state of a crate changed (any property is changed?)?
			 *
			 * @return bool
			 */
			public function isDirty(): bool;

			/**
			 * return an array with dirty properties
			 *
			 * @return IProperty[]
			 */
			public function getDirtyList(): array;
		}
