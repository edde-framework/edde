<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;

	/**
	 * General object which is used to describe relations between objects (not necesarilly database objects) and
	 * theirs hierarchy.
	 */
	interface ICrate {
		/**
		 * schema can be set before crate is used (prepared)
		 *
		 * @param ISchema $schema
		 *
		 * @return ICrate
		 */
		public function setSchema(ISchema $schema): ICrate;

		/**
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * @return IProperty[]
		 */
		public function getPropertyList(): array;

		/**
		 * return list of identity values
		 *
		 * @return IProperty[]
		 */
		public function getIdentifierList(): array;

		/**
		 * add the given value to this property set
		 *
		 * @param IProperty $property
		 * @param bool $force
		 *
		 * @return ICrate
		 */
		public function addProperty(IProperty $property, bool $force = false): ICrate;

		/**
		 * has this property set property with the given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasProperty(string $name): bool;

		/**
		 * return value of the given name
		 *
		 * @param string $name
		 *
		 * @return IProperty
		 */
		public function getProperty(string $name): IProperty;

		/**
		 * set value of the given property; if does not exists exception is thrown
		 *
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return ICrate
		 *
		 * @throws CrateException
		 */
		public function set(string $name, $value): ICrate;

		/**
		 * add scalar value to an array
		 *
		 * @param string $name
		 * @param $value
		 * @param string|int|null $key
		 *
		 * @return ICrate
		 */
		public function add(string $name, $value, $key = null): ICrate;

		/**
		 * put (set) array of values to this crate; this can change state to dirty
		 *
		 * @param array $put
		 * @param bool $strict
		 *
		 * @return ICrate
		 */
		public function put(array $put, bool $strict = true): ICrate;

		/**
		 * push array of values inside this property set; if strict is true, any unknown property will throw exception
		 *
		 * note: this will not make crate dirty
		 *
		 * @param array $push
		 * @param bool $strict
		 *
		 * @return ICrate
		 */
		public function push(array $push, bool $strict = true): ICrate;

		/**
		 * crate will be built dynamically from the input
		 *
		 * @param \Traversable|array $source
		 *
		 * @return ICrate
		 */
		public function dynamic($source): ICrate;

		/**
		 * return value of the given property; if property does not exist, exception is thrown
		 *
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function get(string $name, $default = null);

		/**
		 * has been any value in the property list changed?
		 *
		 * @return bool
		 */
		public function isDirty(): bool;

		/**
		 * return array of dirty values
		 *
		 * @return IProperty[]
		 */
		public function getDirtyList(): array;

		/**
		 * return this crate
		 *
		 * @param string $name
		 * @param ICrate $crate
		 *
		 * @return ICrate
		 */
		public function link(string $name, ICrate $crate): ICrate;

		/**
		 * set crate proxy (when getLink($name), callback will be executed)
		 *
		 * @param string $name
		 * @param callable $crate
		 *
		 * @return ICrate
		 */
		public function proxy(string $name, callable $crate): ICrate;

		/**
		 * link array of links; return this crate
		 *
		 * @param array $linkTo
		 *
		 * @return ICrate
		 */
		public function linkTo(array $linkTo): ICrate;

		/**
		 * has this crate link with a given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasLink(string $name): bool;

		/**
		 * return linked crate; it must already exists or null should be returned
		 *
		 * @param string $name
		 *
		 * @return ICrate|null
		 */
		public function getLink(string $name);

		/**
		 * set the given collection to a crate
		 *
		 * @param string $name
		 * @param ICollection $collection
		 *
		 * @return ICrate
		 */
		public function collection(string $name, ICollection $collection): ICrate;

		/**
		 * return true if the given collection is available
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasCollection(string $name): bool;

		/**
		 * return the given collection or throw an exception
		 *
		 * @param string $name
		 *
		 * @return ICollection
		 */
		public function getCollection(string $name): ICollection;

		/**
		 * dump current crate as an array including collections/links
		 *
		 * @return array
		 */
		public function array(): array;

		/**
		 * a bit tricky method; crate can be used as output of executive method, set all data and than run this commit which should execute the original method.
		 *
		 * @param callable|null $callback
		 *
		 * @return ICrate
		 */
		public function commit(callable $callback = null): ICrate;

		/**
		 * update links, run generators on empty values, ...
		 *
		 * @return ICrate
		 */
		public function update(): ICrate;
	}
