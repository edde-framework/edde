<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\IAttributeList;
	use Edde\Api\Node\INode;

	/**
	 * Default full featured node implementation.
	 */
	class Node extends AbstractNode implements INode {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var mixed
		 */
		protected $value;
		/**
		 * @var IAttributeList
		 */
		protected $attributeList;
		/**
		 * abstract metadata
		 *
		 * @var IAttributeList
		 */
		protected $metaList;

		/**
		 * FORTRAN is not a language. It's a way of turning a multi-million dollar mainframe into a $50 programmable scientific calculator.
		 *
		 * @param string     $name
		 * @param mixed|null $value
		 * @param array      $attributeList
		 */
		public function __construct($name = null, $value = null, array $attributeList = []) {
			parent::__construct();
			$this->name = $name;
			$this->value = $value;
			$this->attributeList = new AttributeList($attributeList);
			$this->metaList = new AttributeList();
		}

		/**
		 * @inheritdoc
		 */
		public function getAttributeList(): IAttributeList {
			return $this->attributeList;
		}

		/**
		 * @inheritdoc
		 */
		public function hasAttribute(string $name): bool {
			return $this->attributeList->has($name);
		}

		/**
		 * @inheritdoc
		 */
		public function getAttribute(string $name, $default = null) {
			return $this->attributeList->get($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function setAttribute(string $name, $value): INode {
			$this->attributeList->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function putAttribute(array $attributeList): INode {
			$this->attributeList->put($attributeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMetaList(): IAttributeList {
			return $this->metaList;
		}

		/**
		 * @inheritdoc
		 */
		public function getMeta(string $name, $default = null) {
			return $this->metaList->get($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function setMeta(string $name, $value): INode {
			$this->metaList->set($name, $value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function putMeta(array $metaList): INode {
			$this->metaList->put($metaList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getPath($attribute = false, $meta = false) {
			$current = $this;
			$path = [];
			while ($current) {
				$fragment = $current->getName();
				if ($attribute && empty($arrayList = $current->attributeList->array()) === false) {
					$fragment .= '[' . implode('][', array_keys($arrayList)) . ']';
				}
				if ($meta && empty($metaList = $current->metaList->array()) === false) {
					$fragment .= '(' . implode(')(', array_keys($metaList)) . ')';
				}
				if (($parent = $current->getParent()) && ($index = array_search($current, $parent->getNodeList(), true)) !== false) {
					$fragment .= ':' . $index;
				}
				$path[] = $fragment;
				$current = $current->getParent();
			}
			return '/' . implode('/', array_reverse($path));
		}

		/**
		 * @inheritdoc
		 */
		public function setName(string $name) {
			$this->name = $name;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function setValue($value) {
			$this->value = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getValue($default = null) {
			return $this->value !== null ? $this->value : $default;
		}

		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof INode;
		}

		public function __clone() {
			parent::__clone();
			$this->attributeList = clone $this->attributeList;
			$this->metaList = clone $this->metaList;
			if (is_object($this->value)) {
				$this->value = clone $this->value;
			}
		}

		/**
		 * @param string     $name
		 * @param array      $attributeList
		 * @param mixed|null $value
		 *
		 * @return INode|static
		 */
		static public function create($name = null, $value = null, array $attributeList = []) {
			return new static($name, $value, $attributeList);
		}
	}
