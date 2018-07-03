<?php
	declare(strict_types=1);

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
		public function setName(string $name);

		/**
		 * @return string
		 */
		public function getName();

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
		 * return list of attributes for this node
		 *
		 * @return IAttributeList
		 */
		public function getAttributeList(): IAttributeList;

		/**
		 * is the given attribute name present?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasAttribute(string $name): bool;

		/**
		 * return attribute
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return IAttributeList|string|mixed
		 */
		public function getAttribute(string $name, $default = null);

		/**
		 * set attribute value or attribute list as a namespaced attribute
		 *
		 * @param string               $name
		 * @param IAttributeList|mixed $value
		 *
		 * @return INode
		 */
		public function setAttribute(string $name, $value): INode;

		/**
		 * @param array $attributeList
		 *
		 * @return INode
		 */
		public function putAttributeList(array $attributeList): INode;

		/**
		 * @param array $attributeList
		 *
		 * @return INode
		 */
		public function appendAttributeList(array $attributeList): INode;

		/**
		 * return list of meta data
		 *
		 * @return IAttributeList
		 */
		public function getMetaList(): IAttributeList;

		/**
		 * get meta from node
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return IAttributeList|string|int|mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * @param string               $name
		 * @param IAttributeList|mixed $value
		 *
		 * @return INode
		 */
		public function setMeta(string $name, $value): INode;

		/**
		 * @param array $metaList
		 *
		 * @return INode
		 */
		public function putMetaList(array $metaList): INode;

		/**
		 * @param array $metaList
		 *
		 * @return INode
		 */
		public function appendMetaList(array $metaList): INode;

		/**
		 * generate materialized path from node names
		 *
		 * @return INode|null
		 */
		public function getParent();

		/**
		 * @param bool $attributes include attribute names (e.g. [foo][bar], ....)
		 * @param bool $meta       include meta names (e.g. (foo)(bar)
		 *
		 * @return string
		 */
		public function getPath($attributes = false, $meta = false);

		/**
		 * @return INode[]
		 */
		public function getNodeList(): array;
	}
