<?php
	namespace Edde\Api\Crate;

	/**
	 * Crate property.
	 */
		interface IProperty {
			/**
			 * has property been changed?
			 *
			 * @return bool
			 */
			public function isDirty(): bool;
		}
