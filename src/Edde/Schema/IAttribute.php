<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	/**
	 * An attribute is "property" of schema which is basically
	 * definition of a "real" property describing it's metadata.
	 */
	interface IAttribute {
		/**
		 * get attribute name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * get type of an attribute
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * is this property marked as primary?
		 *
		 * @return bool
		 */
		public function isPrimary(): bool;

		/**
		 * is this property marked as unique?
		 *
		 * @return bool
		 */
		public function isUnique(): bool;

		/**
		 * @return bool
		 */
		public function isRequired(): bool;

		/**
		 * @return bool
		 */
		public function isLink(): bool;

		/**
		 * return generator name for this attribute
		 *
		 * @return string
		 */
		public function getGenerator(): ?string;

		/**
		 * return filter name for this attribute
		 *
		 * @return null|string
		 */
		public function getFilter(): ?string;

		/**
		 * return name of the sanitizer for this property
		 *
		 * @return null|string
		 */
		public function getSanitizer(): ?string;

		/**
		 * return name of the validator for this property, if any
		 *
		 * @return null|string
		 */
		public function getValidator(): ?string;

		/**
		 * @return mixed
		 */
		public function getDefault();
	}
