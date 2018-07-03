<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Object\Object;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Set of tools for work with nodes.
	 */
	class NodeUtils extends Object {
		/**
		 * @param INode                        $root
		 * @param \Traversable|\Iterator|array $source
		 *
		 * @return INode
		 * @throws NodeException
		 */
		static public function node(INode $root, $source): INode {
			$callback = null;
			if (is_array($source) === false && is_object($source) === false) {
				throw new NodeException('Source must be array or stdClass object.');
			}
			/** @noinspection UnnecessaryParenthesesInspection */
			return ($callback = function (callable $callback, INode $root, $source) {
				$attributeList = $root->getAttributeList();
				/** @noinspection ForeachSourceInspection */
				foreach ($source as $key => $value) {
					switch ($key) {
						case 'name':
							$root->setName($value);
							continue 2;
						case 'value':
							$root->setValue($value);
							continue 2;
						case 'attribute-list':
							$attributeList->put((array)$value);
							continue 2;
						case 'meta-list':
							$root->getMetaList()->put((array)$value);
							continue 2;
						case 'node-list':
							/** @noinspection ForeachSourceInspection */
							foreach ($value as $item) {
								/** @noinspection DisconnectedForeachInstructionInspection */
								$root->addNode($node = new Node());
								if (is_object($item) || is_array($item)) {
									$callback($callback, $node, $item);
									continue;
								}
								$node->setValue($item);
							}
							continue 2;
					}
					if (is_object($value)) {
						$value = [
							$value,
						];
					}
					if (is_array($value)) {
						$root->addNode($itemList = new Node($key));
						foreach ($value as $item) {
							/** @noinspection DisconnectedForeachInstructionInspection */
							$itemList->addNode($node = new Node());
							if (is_object($item) || is_array($item)) {
								$callback($callback, $node, $item);
								continue;
							}
							$node->setValue($item);
						}
						continue;
					}
					$attributeList->set($key, $value);
				}
				return $root;
			})($callback, $root, $source);
		}

		/**
		 * convert input of stdClass to node tree
		 *
		 * @param \stdClass   $stdClass
		 * @param INode       $node
		 * @param string|null $class
		 *
		 * @return INode
		 * @throws NodeException
		 */
		static public function toNode(\stdClass $stdClass, INode $node = null, string $class = null): INode {
			$createNode = function (string $class, string $name = null): INode {
				/** @var $node INode */
				if (($node = new $class()) instanceof INode === false) {
					throw new ClassMismatchException(sprintf('Class specified [%s] is not instance of [%s].', $class, INode::class));
				}
				$name ? $node->setName($name) : null;
				return $node;
			};
			$node = $node ?: $createNode($class = $class ?: Node::class);
			foreach ($stdClass as $k => $v) {
				if ($k === '::name') {
					$node->setName($v);
					continue;
				} else if ($k === '::value') {
					$node->setValue($v);
					continue;
				} else if ($k === '::meta') {
					$node->putMetaList((array)$v);
					continue;
				} else if ($v instanceof \stdClass) {
					$node->addNode(self::toNode($v, $createNode($class, $k), $class));
					continue;
				} else if (is_array($v)) {
					foreach ($v as $vv) {
						$node->addNode(self::toNode($vv, $createNode($class, $k), $class));
					}
					continue;
				}
				$node->setAttribute($k, $v);
			}
			/** @var $node INode */
			if ($node->getName() === null && $node->getNodeCount() === 1) {
				$node = $node->getNodeList()[0];
				$node->setParent(null);
				return $node;
			}
			return $node;
		}

		/**
		 * convert the given node to stdClass; output of this method should be convertible 1:1 by self::toNode()
		 *
		 * @param INode $root
		 *
		 * @return \stdClass
		 */
		static public function fromNode(INode $root): \stdClass {
			$object = new \stdClass();
			$attributeList = $root->getAttributeList();
			if (($value = $root->getValue()) !== null) {
				$object->{'::value'} = $value;
			}
			if ($attributeList->isEmpty() === false) {
				$object = (object)array_merge((array)$object, $attributeList->array());
			}
			$metaList = $root->getMetaList();
			if ($metaList->isEmpty() === false) {
				$object->{'::meta'} = $metaList->array();
			}
			if ($value = $root->getValue()) {
				$object->value = $value;
			}
			$nodeList = [];
			foreach ($root->getNodeList() as $node) {
				$nodeList[$node->getName()][] = self::fromNode($node);
			}
			foreach ($nodeList as $name => $list) {
				$object->{$name} = count($list) === 1 ? reset($list) : $list;
			}
			return $root->isRoot() ? (object)[$root->getName() => $object] : $object;
		}

		/**
		 * namespecize the given node tree; attributes matching the given preg will be converted to namespace structure
		 *
		 * @param INode  $root
		 * @param string $preg
		 *
		 * @throws NodeException
		 * @throws StringException
		 */
		static public function namespace(INode $root, string $preg) {
			foreach (NodeIterator::recursive($root, true) as $node) {
				$attributeList = $node->getAttributeList();
				foreach ($attributeList as $k => $value) {
					if (($match = StringUtils::match($k, $preg, true)) !== null) {
						$attributeList->set($match['namespace'], $namespace = $attributeList->get($match['namespace'], new AttributeList()));
						$namespace->set($match['name'], $value);
						$attributeList->remove($k);
					}
				}
			}
		}
	}
