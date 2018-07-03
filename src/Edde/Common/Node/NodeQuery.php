<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Common\Object\Object;
	use Iterator;

	class NodeQuery extends Object implements INodeQuery {
		/**
		 * @var string
		 */
		protected $query;
		protected $filter;

		/**
		 * @param string $query
		 */
		public function __construct($query) {
			$this->query = $query;
		}

		/**
		 * return first result of a query or null
		 *
		 * @param INode               $root
		 * @param string              $query
		 * @param mixed|callable|null $default
		 *
		 * @return INode|null|mixed
		 */
		static public function first(INode $root, $query, $default = null) {
			$node = null;
			/** @noinspection LoopWhichDoesNotLoopInspection */
			foreach (self::node($root, $query) as $node) {
				break;
			}
			return $node ?: (is_callable($default) ? call_user_func($default) : $default);
		}

		/**
		 * @param INode  $node
		 * @param string $query
		 *
		 * @return INode[]
		 */
		static public function node(INode $node, $query) {
			return self::create($query)->filter($node);
		}

		/**
		 * @param string $query
		 *
		 * @return INodeQuery|INode[]
		 */
		static public function create($query) {
			return new self($query);
		}

		/**
		 * return last result of a query or null
		 *
		 * @param INode               $node
		 * @param string              $query
		 * @param mixed|callable|null $default
		 *
		 * @return INode|null|mixed
		 */
		static public function last(INode $node, $query, $default = null) {
			$n = null;
			/** @noinspection PhpStatementHasEmptyBodyInspection */
			foreach (self::node($node, $query) as $n) {
				;
			}
			return $n ?: (is_callable($default) ? call_user_func($default) : $default);
		}

		public function isEmpty(INode $node) {
			$iterator = $this->filter($node);
			$iterator->rewind();
			return $iterator->valid() === false;
		}

		public function filter(INode $node) {
			return $this->query(NodeIterator::recursive($node));
		}

		public function query(Iterator $iterator) {
			if ($this->filter === null) {
				$this->filter = $this->parse($this->query);
			}
			/** @var $node INode */
			foreach ($iterator as $node) {
				$level = $node->getLevel();
				if ($level < $this->filter->min) {
					continue;
				}
				if ($this->filter->fixed && $level !== $this->filter->level) {
					continue;
				}
				$path = $node->getPath($this->filter->attributes, $this->filter->metas);
				if (preg_match($this->filter->preg, $path) !== 1) {
					continue;
				}
				yield $node;
			}
		}

		/**
		 * token generator
		 *
		 * @param string $query
		 *
		 * @return \stdClass
		 */
		protected function parse($query) {
			$query = str_replace('//', '/*/', $query);
			$query = '/' . trim($query, '/') . '/';
			$filter = new \stdClass();
			$filter->fixed = strpos($query, '**') === false && strpos($query, '?*') === false;
			$filter->static = strpos($query, '*') === false;
			if ($filter->fixed) {
				$filter->level = substr_count($query, '/') - 2;
			}
			$filter->attributes = strpos($query, '[') !== false && strpos($query, ']') !== false;
			$filter->metas = strpos($query, '(') !== false && strpos($query, ')') !== false;
			$filter->min = $filter->fixed ? $filter->level : min(($result = array_search('**', $explode = explode('/', trim($query, '/')), true)) === false ? PHP_INT_MAX : $result, ($result = array_search('?*', $explode, true)) === false ? PHP_INT_MAX : $result);

			$preg = null;
			foreach (explode('/', trim($query, '/')) as $fragment) {
				$name = 'a-zA-Z0-9_\\\\-';
				if ($fragment === '**') {
					$preg .= '.*?/';
					continue;
				} else if ($fragment === '?*') {
					$preg .= '(.*?/)?';
					continue;
				} else if ($fragment === '*') {
					$preg .= '[' . $name . ']*(\\[.*?\\])*(\\(.*?\\))*(:\d+)?/';
					continue;
				}
				$fragment = str_replace([
					'[',
					']',
					'(',
					')',
				], [
					'\\[',
					'\\]',
					'\\(',
					'\\)',
				], $fragment);
				if (preg_match('~^(\\\\[[' . $name . ']+\\\\])+$~', $fragment)) {
					$fragment = '.*?' . $fragment;
				}
				if (preg_match('~^[' . $name . ']+$~', $fragment)) {
					$fragment .= '(\\[.*?\\])*(\\(.*?\\))*';
				}
				$preg .= $fragment . '(:\d+)?/';
			}

			$filter->preg = '~^/' . rtrim($preg, '/') . '$~';

			return $filter;
		}
	}
