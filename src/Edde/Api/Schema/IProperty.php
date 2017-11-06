<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

		interface IProperty {
			/**
			 * get name of this property
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * get type of property
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
			 * @return string
			 */
			public function getGenerator(): ?string;

			/**
			 * get property filter
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
			 * is this property marked as unique?
			 *
			 * @return bool
			 */
			public function isUnique(): bool;

			/**
			 * @return bool
			 */
			public function isRequired(): bool;
		}
