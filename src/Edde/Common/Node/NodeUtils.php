<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;

	/**
	 * Set of tools for work with nodes.
	 */
	class NodeUtils extends AbstractObject {
		/**
		 * @param INode $root
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
							$root->addAttributeList((array)$value);
							continue 2;
						case 'meta-list':
							$root->addMetaList((array)$value);
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
					$root->setAttribute($key, $value);
				}
				return $root;
			})($callback, $root, $source);
		}

		/**
		 * convert input of stdClass to node tree
		 *
		 * @param \stdClass $stdClass
		 * @param INode $root
		 *
		 * @return INode
		 * @throws NodeException
		 */
		static public function convert(\stdClass $stdClass, INode $root = null): INode {
			$root = $root ?: new Node();
			/** @noinspection ForeachSourceInspection */
			foreach ($stdClass as $k => $v) {
				if ($v instanceof \stdClass) {
					$root->addNode($node = new Node($k));
					self::convert($v, $node);
					continue;
				} else if (is_array($v)) {
					$root->addNode($node = new Node($k));
					/** @noinspection ForeachSourceInspection */
					foreach ($v as $kk => $vv) {
						$node->addNode(self::convert($vv, new Node($kk)));
					}
					continue;
				}
				$root->setAttribute($k, $v);
			}
			return $root;
		}
	}
