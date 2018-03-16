<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Config\IConfigurable;

	interface ISchema extends IConfigurable {
		/**
		 * return name of a schema (it could have even "namespace" like name)
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * has this schema an alias?
		 *
		 * @return bool
		 */
		public function hasAlias(): bool;

		/**
		 * get alias of this schema
		 *
		 * @return string
		 */
		public function getAlias(): string;

		/**
		 * return alias or the original schema name
		 *
		 * @return string
		 */
		public function getRealName(): string;

		/**
		 * special kind of schemas used just as a relational schemas (m:n relations, for graphs, ...)
		 *
		 * @return bool
		 */
		public function isRelation(): bool;

		/**
		 * @param string $name
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getProperty(string $name): IProperty;

		/**
		 * return list of properties of this schema
		 *
		 * @return IProperty[]
		 */
		public function getProperties(): array;

		/**
		 * has schema a primary property?
		 *
		 * @return bool
		 */
		public function hasPrimary(): bool;

		/**
		 * return a primary property of this schema (should throw an exception
		 * when there are more primary properties)
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getPrimary(): IProperty;

		/**
		 * return list of unique properties
		 *
		 * @return IProperty[]
		 */
		public function getUniques(): array;

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
		public function getLinks(string $schema = null): array;

		/**
		 * is there a link to the given schema? (could be more than one link)
		 *
		 * @param string $schema
		 *
		 * @return bool
		 */
		public function hasLink(string $schema): bool;

		/**
		 * get link to the given schema or throw an exception when there is no or multiple links
		 *
		 * @param string $schema
		 *
		 * @return ILink
		 *
		 * @throws SchemaException
		 */
		public function getLink(string $schema): ILink;

		/**
		 * add a new relation to this schema (relation is jump through another schema to the target)
		 *
		 * @param IRelation $relation
		 *
		 * @return ISchema
		 */
		public function relation(IRelation $relation): ISchema;

		/**
		 * return all relations or relations to the given schema
		 *
		 * @param string|null $schema
		 *
		 * @return IRelation[]
		 */
		public function getRelations(string $schema = null): array;

		/**
		 * is there any relation to the given schema?
		 *
		 * @param string $schema
		 *
		 * @return bool
		 */
		public function hasRelation(string $schema): bool;

		/**
		 * shorthand to get a relation; if there are more relations, exception should be thrown
		 *
		 * @param string      $schema
		 * @param string|null $relation optional relation name if there are more relations between targets
		 *
		 * @return IRelation
		 *
		 * @throws SchemaException
		 */
		public function getRelation(string $schema, string $relation = null): IRelation;
	}
