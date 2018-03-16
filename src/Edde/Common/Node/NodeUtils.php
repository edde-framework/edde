<?php
	declare(strict_types=1);
	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Object;
	use ReflectionClass;
	use stdClass;

	/**
	 * Set of tools for work with nodes.
	 */
	class NodeUtils extends Object {
		/**
		 * @param INode    $root
		 * @param iterable $source
		 *
		 * @return INode
		 * @throws \Edde\Node\NodeException
		 */
		static public function node(INode $root, iterable $source): INode {
			$callback = null;
			if (is_array($source) === false && is_object($source) === false) {
				throw new \Edde\Node\NodeException('Source must be array or stdClass object.');
			}
			/** @noinspection UnnecessaryParenthesesInspection */
			return ($callback = function (callable $callback, INode $root, $source) {
				$attributeList = $root->getAttributes();
				/** @noinspection ForeachSourceInspection */
				foreach ($source as $key => $value) {
					switch ($key) {
						case 'name':
							$root->setName($value);
							continue 2;
						case 'value':
							$root->setValue($value);
							continue 2;
						case 'attributes':
							$attributeList->put((array)$value);
							continue 2;
						case 'metas':
							$root->getMetas()->put((array)$value);
							continue 2;
						case 'nodes':
							/** @noinspection ForeachSourceInspection */
							foreach ($value as $item) {
								/** @noinspection DisconnectedForeachInstructionInspection */
								$root->add($node = new Node());
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
						$root->add($itemList = new Node($key));
						foreach ($value as $item) {
							/** @noinspection DisconnectedForeachInstructionInspection */
							$itemList->add($node = new Node());
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
		 * @param stdClass    $stdClass
		 * @param INode       $node
		 * @param string|null $class
		 *
		 * @return INode
		 * @throws \Edde\Node\NodeException
		 */
		static public function toNode(stdClass $stdClass, INode $node = null, string $class = null): INode {
			if (($reflectionClass = new ReflectionClass($class = $class ?: Node::class))->implementsInterface(INode::class)) {
				throw new ClassMismatchException(sprintf('Class specified [%s] is not instance of [%s].', $class, INode::class));
			} else if (($constructor = $reflectionClass->getConstructor()) && $constructor->getNumberOfRequiredParameters() > 0) {
				throw new \Edde\Node\NodeException(sprintf('Node class [%s] must not require any parameters in constructor in order to be used in [%s].', $class, __METHOD__));
			}
			$createNode = function (string $class, string $name = null): INode {
				/** @var $node INode */
				$node = new $class();
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
					$node->putMetas((array)$v);
					continue;
				} else if ($v instanceof stdClass) {
					$node->add(self::toNode($v, $createNode($class, $k), $class));
					continue;
				} else if (is_array($v)) {
					foreach ($v as $vv) {
						$node->add(self::toNode($vv, $createNode($class, $k), $class));
					}
					continue;
				}
				$node->setAttribute($k, $v);
			}
			/** @var $node INode */
			if ($node->getName() === null && $node->count() === 1) {
				$node = $node->getTrees()[0];
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
		 * @return stdClass
		 */
		static public function fromNode(INode $root): stdClass {
			$object = new stdClass();
			$attributeList = $root->getAttributes();
			if (($value = $root->getValue()) !== null) {
				$object->{'::value'} = $value;
			}
			if ($attributeList->isEmpty() === false) {
				$object = (object)array_merge((array)$object, $attributeList->array());
			}
			$metaList = $root->getMetas();
			if ($metaList->isEmpty() === false) {
				$object->{'::meta'} = $metaList->array();
			}
			if ($value = $root->getValue()) {
				$object->value = $value;
			}
			$nodeList = [];
			foreach ($root as $node) {
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
		 * @throws \Edde\Node\NodeException
		 */
		static public function namespace(INode $root, string $preg) {
			foreach (TreeIterator::recursive($root, true) as $node) {
				$attributeList = $node->getAttributes();
				foreach ($attributeList as $k => $value) {
					if (($match = preg_match($preg, $k)) !== null && isset($match['namespace'], $match['name'])) {
						$attributeList->set($match['namespace'], $namespace = $attributeList->get($match['namespace'], new Attributes()));
						$namespace->set($match['name'], $value);
						$attributeList->remove($k);
					}
				}
			}
		}
	}
