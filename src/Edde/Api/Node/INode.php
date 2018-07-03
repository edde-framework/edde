<?php
	declare(strict_types = 1);

	namespace Edde\Api\Node;

	/**
	 * INode is extended version of IAbstractNode which holds name, value, attributes and metadata; it can be used as
	 * complex (tree) data structure holder. It is similar to XML node.
	 */
	interface INode extends IAbstractNode {
		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName($name);

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * generate materialized path from node names
		 *
		 * @return INode
		 */
		public function getParent();

		/**
		 * @param bool $attributes include attribute names (e.g. [foo][bar], ....)
		 * @param bool $meta include meta names (e.g. (foo)(bar)
		 *
		 * @return string
		 */
		public function getPath($attributes = false, $meta = false);

		/**
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setValue($value);

		/**
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getValue($default = null);

		/**
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setAttribute($name, $value);

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttribute($name);

		/**
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getAttribute($name, $default = null);

		/**
		 * remove the given attribute from list
		 *
		 * @param string $name
		 *
		 * @return INode
		 */
		public function removeAttribute(string $name): INode;

		/**
		 * replace current attribute list by the given one
		 *
		 * @param array $attributeList
		 *
		 * @return $this
		 */
		public function setAttributeList(array $attributeList);

		/**
		 * add the given attribute list to the current one (overriding same keys)
		 *
		 * @param array $attributeList
		 *
		 * @return $this
		 */
		public function addAttributeList(array $attributeList);

		/**
		 * has this node attributes with a given namespace
		 *
		 * @param string $namespace
		 *
		 * @return bool
		 */
		public function hasAttributeList(string $namespace): bool;

		/**
		 * @param string $namespace
		 *
		 * @return array
		 */
		public function getAttributeList(string $namespace = null): array;

		public function removeAttributeList(string $namespace): INode;

		/**
		 * @param array $metaList
		 *
		 * @return $this
		 */
		public function setMetaList(array $metaList);

		/**
		 * @param array $metaList
		 *
		 * @return $this
		 */
		public function addMetaList(array $metaList);

		/**
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setMeta($name, $value);

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasMeta($name);

		/**
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getMeta($name, $default = null);

		/**
		 * @return array
		 */
		public function getMetaList();

		/**
		 * @return INode[]
		 */
		public function getNodeList(): array;
	}
