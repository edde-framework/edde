<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Node\INode;

		interface IProperty {
			/**
			 * get name of this property
			 *
			 * @return string
			 */
			public function getName(): string;

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
			 * is this property marked as unique?
			 *
			 * @return bool
			 */
			public function isUnique(): bool;

			/**
			 * get type of property
			 *
			 * @return string
			 */
			public function getType(): string;

			/**
			 * is this property a link?
			 *
			 * @return bool
			 */
			public function isLink(): bool;

			/**
			 * @return INode
			 */
			public function getLinkNode(): INode;
		}
