<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\UnknownPropertyException;

		interface ISchema extends IConfigurable {
			/**
			 * return name of a schema (it could have even "namespace" like name)
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * special kind of schemas used just as a relational schemas (m:n relations, for graphs, ...)
			 *
			 * @return bool
			 */
			public function isRelation(): bool;

			/**
			 * has this schema alias?
			 *
			 * @return bool
			 */
			public function hasAlias(): bool;

			/**
			 * return schema alias
			 *
			 * @return string|null
			 */
			public function getAlias(): ?string;

			/**
			 * @param string $name
			 *
			 * @return IProperty
			 *
			 * @throws UnknownPropertyException
			 */
			public function getProperty(string $name): IProperty;

			/**
			 * return list of properties of this schema
			 *
			 * @return IProperty[]
			 */
			public function getPropertyList(): array;

			/**
			 * return an array with all primary properties
			 *
			 * @return IProperty[]
			 */
			public function getPrimaryList(): array;

			/**
			 * return a primary property of this schema (should throw an exception
			 * when there are more primary properties)
			 *
			 * @return IProperty
			 */
			public function getPrimary(): IProperty;

			/**
			 * return list of unique properties
			 *
			 * @return IProperty[]
			 */
			public function getUniqueList(): array;

			/**
			 * return an array with properties which are link
			 *
			 * @return IProperty[]
			 */
			public function getLinkList(): array;

			/**
			 * @param string $property
			 *
			 * @return ILink
			 */
			public function getLink(string $property): ILink;

			/**
			 * get relation (link) property list for the given target
			 *
			 * @param string $target
			 *
			 * @return IProperty[]
			 */
			public function getRelationList(string $target): array;

			/**
			 * return list of property nodes
			 *
			 * @return INode[]
			 */
			public function getNodeList(): array;
		}
