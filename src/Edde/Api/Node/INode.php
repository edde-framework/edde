<?php
	declare(strict_types=1);
	namespace Edde\Api\Node;

	use Traversable;

	/**
	 * INode is extended version of ITree which holds name, value, attributes and metadata; it can be used as
	 * complex (tree) data structure holder. It is similar to XML node.
	 */
	interface INode extends ITree {
		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(?string $name): INode;

		/**
		 * @return string
		 */
		public function getName(): ?string;

		/**
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setValue($value): INode;

		/**
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getValue($default = null);

		/**
		 * return list of attributes for this node
		 *
		 * @return IAttributes
		 */
		public function getAttributes(): IAttributes;

		/**
		 * set attribute value or attribute list as a namespaced attribute
		 *
		 * @param string            $name
		 * @param IAttributes|mixed $value
		 *
		 * @return INode
		 */
		public function setAttribute(string $name, $value): INode;

		/**
		 * @param array $attributes
		 *
		 * @return INode
		 */
		public function putAttributes(array $attributes): INode;

		/**
		 * @param array $attributes
		 *
		 * @return INode
		 */
		public function mergeAttributes(array $attributes): INode;

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
		 * @return IAttributes|string|mixed
		 */
		public function getAttribute(string $name, $default = null);

		/**
		 * return list of meta data
		 *
		 * @return IAttributes
		 */
		public function getMetas(): IAttributes;

		/**
		 * @param string            $name
		 * @param IAttributes|mixed $value
		 *
		 * @return INode
		 */
		public function setMeta(string $name, $value): INode;

		/**
		 * @param array $metaList
		 *
		 * @return INode
		 */
		public function putMetas(array $metaList): INode;

		/**
		 * @param array $metas
		 *
		 * @return INode
		 */
		public function mergeMetas(array $metas): INode;

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasMeta(string $name): bool;

		/**
		 * get meta from node
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return IAttributes|string|int|mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * generate materialized path from node names
		 *
		 * @return INode|null
		 */
		public function getParent(): ?ITree;

		/**
		 * is node with the given name in current node list?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasNode(string $name): bool;

		/**
		 * stranger between the others: get the node with the given name; if node does not
		 * exists, new one is created
		 *
		 * @param string $name
		 *
		 * @return INode
		 */
		public function getNode(string $name): INode;

		/**
		 * @return INode[]
		 */
		public function getTrees(): array;

		/**
		 * @return Traversable|INode[]
		 */
		public function getIterator();
	}
