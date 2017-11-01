<?php
	declare(strict_types=1);
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
			 * return list of property nodes
			 *
			 * @return INode[]
			 */
			public function getNodeList(): array;

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
			 * link $this schema to the target $schema using the given $link (relation is
			 * from "him" to "me")
			 *
			 * @param ILink $link
			 *
			 * @return ISchema
			 */
			public function linkTo(ILink $link): ISchema;

			/**
			 * return list of links pointing to the given schema
			 *
			 * @param string $schema
			 *
			 * @return ILink[]
			 */
			public function getLinkToList(string $schema = null): array;

			/**
			 * link $schema to $this schema; the relation is from "me" to "him"
			 *
			 * @param ILink $link
			 *
			 * @return ISchema
			 */
			public function link(ILink $link): ISchema;

			/**
			 * get all links pointing to the given schema (related to self::link() method)
			 *
			 * @param string $schema
			 *
			 * @return ILink[]
			 */
			public function getLinkList(string $schema = null): array;

			/**
			 * add a m:n relation to another schema through (relation) schema
			 *
			 * @param IRelation $relation
			 *
			 * @return ISchema
			 */
			public function relationTo(IRelation $relation): ISchema;

			/**
			 * return array of relations to the given schema (the given schema is target schema, not
			 * relation schema)
			 *
			 * @param string $schema
			 *
			 * @return IRelation[]
			 */
			public function getRelationList(string $schema): array;
		}
