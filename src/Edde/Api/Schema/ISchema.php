<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	use Edde\Api\Deffered\IDeffered;

	interface ISchema extends IDeffered {
		/**
		 * return only the name of this schema without namespace
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return schema's namespace; this can be null
		 *
		 * @return string
		 */
		public function getNamespace(): string;

		/**
		 * return full name of this schema (including namespace, ...)
		 *
		 * @return string
		 */
		public function getSchemaName(): string;

		/**
		 * tells if given property name is known in this schema
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasProperty(string $name): bool;

		/**
		 * retrieve the given property; throws exception if the property is not known for this schema
		 *
		 * @param string $name
		 *
		 * @return ISchemaProperty
		 *
		 * @throws SchemaException
		 */
		public function getProperty(string $name): ISchemaProperty;

		/**
		 * return set of properties of this Schema
		 *
		 * @return ISchemaProperty[]
		 */
		public function getPropertyList(): array;

		/**
		 * make a link between the given source and destination property (1:1); if the name is present and force === false, exception should be thrown
		 *
		 * @param string $name
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 * @param bool $force
		 *
		 * @return ISchema
		 */
		public function link(string $name, ISchemaProperty $source, ISchemaProperty $target, bool $force = false): ISchema;

		/**
		 * is there link with the given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasLink(string $name): bool;

		/**
		 * return a link with the given name
		 *
		 * @param string $name
		 *
		 * @return ISchemaLink
		 */
		public function getLink(string $name): ISchemaLink;

		/**
		 * return all known links in this schema
		 *
		 * @return ISchemaLink[]
		 */
		public function getLinkList(): array;

		/**
		 * connect the given source property to the target property as 1:n collection
		 *
		 * @param string $name
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 * @param bool $force
		 *
		 * @return ISchema
		 */
		public function collection(string $name, ISchemaProperty $source, ISchemaProperty $target, bool $force = false): ISchema;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasCollection(string $name): bool;

		/**
		 * @param string $name
		 *
		 * @return ISchemaCollection
		 */
		public function getCollection(string $name): ISchemaCollection;

		/**
		 * @return ISchemaCollection[]
		 */
		public function getCollectionList(): array;

		/**
		 * link the given source property to the given target property in both directions (link + collection in reverse); this is only shorthand for link(source, target) + collection(target, source)
		 *
		 * @param string $link
		 * @param string $collection
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 *
		 * @return ISchema
		 */
		public function linkTo(string $link, string $collection, ISchemaProperty $source, ISchemaProperty $target): ISchema;

		/**
		 * retrieve named metadata
		 *
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * return current set of metadata
		 *
		 * @return array
		 */
		public function getMetaList(): array;

		/**
		 * is the given meta key set?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasMeta(string $name): bool;
	}
