<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\INode;

	/**
	 * Default full featured node implementation.
	 */
	class Node extends AbstractNode implements INode {
		/**
		 * @var string
		 */
		protected $name;
		protected $attributeList = [];
		protected $attributeNamespaceList = [];
		/**
		 * @var mixed
		 */
		protected $value;
		/**
		 * abstract metadata
		 *
		 * @var array
		 */
		protected $metaList = [];

		/**
		 * FORTRAN is not a language. It's a way of turning a multi-million dollar mainframe into a $50 programmable scientific calculator.
		 *
		 * @param string $name
		 * @param mixed|null $value
		 * @param array $attributeList
		 */
		public function __construct($name = null, $value = null, array $attributeList = []) {
			parent::__construct();
			$this->name = $name;
			$this->value = $value;
			$this->attributeList = $attributeList;
		}

		/**
		 * @param string $name
		 * @param array $attributeList
		 * @param mixed|null $value
		 *
		 * @return INode|static
		 */
		static public function create($name = null, $value = null, array $attributeList = []) {
			return new static($name, $value, $attributeList);
		}

		/**
		 * @inheritdoc
		 */
		public function getPath($attribute = false, $meta = false) {
			$current = $this;
			$path = [];
			while ($current) {
				$fragment = $current->getName();
				if ($attribute && empty($current->attributeList) === false) {
					$fragment .= '[' . implode('][', array_keys($current->attributeList)) . ']';
				}
				if ($meta && empty($current->metaList) === false) {
					$fragment .= '(' . implode(')(', array_keys($current->metaList)) . ')';
				}
				$path[] = $fragment;
				$current = $current->getParent();
			}
			return '/' . implode('/', array_reverse($path));
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
		public function setName($name) {
			$this->name = $name;
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
		public function setValue($value) {
			$this->value = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasAttribute($name) {
			return isset($this->attributeList[$name]) || array_key_exists($name, $this->attributeList);
		}

		/**
		 * @inheritdoc
		 */
		public function getAttribute($name, $default = null) {
			return $this->attributeList[$name] ?? $default;
		}

		/**
		 * @inheritdoc
		 */
		public function removeAttribute(string $name): INode {
			$this->attributeNamespaceList = [];
			unset($this->attributeList[$name]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasAttributeList(string $namespace): bool {
			$this->getAttributeList($namespace);
			return empty($this->attributeNamespaceList[$namespace]) === false;
		}

		/**
		 * @inheritdoc
		 */
		public function getAttributeList(string $namespace = null): array {
			if (isset($this->attributeNamespaceList[$namespace]) === false) {
				$key = $namespace ? "$namespace:" : '';
				$this->attributeNamespaceList[$namespace] = [];
				foreach ($this->attributeList as $name => $value) {
					if ($key !== '' && strpos($name, $key) === false) {
						continue;
					}
					$this->attributeNamespaceList[$namespace][str_replace($key, '', $name)] = $value;
				}
			}
			return $this->attributeNamespaceList[$namespace];
		}

		/**
		 * @inheritdoc
		 */
		public function setAttributeList(array $attributeList) {
			$this->attributeNamespaceList = [];
			$this->attributeList = $attributeList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function removeAttributeList(string $namespace): INode {
			unset($this->attributeNamespaceList[$namespace]);
			$key = "$namespace:";
			foreach ($this->attributeList as $name => $value) {
				if (strpos($name, $key) === false) {
					continue;
				}
				unset($this->attributeList[$name]);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addAttributeList(array $attributeList) {
			$this->attributeNamespaceList = [];
			foreach ($attributeList as $name => $value) {
				$this->setAttribute($name, $value);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAttribute($name, $value) {
			$this->attributeNamespaceList = [];
			$this->attributeList[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasMeta($name) {
			return isset($this->metaList[$name]) || array_key_exists($name, $this->metaList);
		}

		/**
		 * @inheritdoc
		 */
		public function getMeta($name, $default = null) {
			return $this->metaList[$name] ?? $default;
		}

		/**
		 * @inheritdoc
		 */
		public function getMetaList() {
			return $this->metaList;
		}

		/**
		 * @inheritdoc
		 */
		public function setMetaList(array $metaList) {
			$this->metaList = $metaList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addMetaList(array $metaList) {
			foreach ($metaList as $name => $value) {
				$this->setMeta($name, $value);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setMeta($name, $value) {
			$this->metaList[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof INode;
		}

		public function __clone() {
			parent::__clone();
			$this->attributeNamespaceList = [];
			foreach ($this->attributeList as $k => &$v) {
				if (is_object($v)) {
					$v = clone $v;
				}
			}
			unset($v);
			foreach ($this->metaList as $k => &$v) {
				if (is_object($v)) {
					$v = clone $v;
				}
			}
			unset($v);
			if (is_object($this->value)) {
				$this->value = clone $this->value;
			}
		}
	}
