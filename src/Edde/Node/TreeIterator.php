<?php
declare(strict_types=1);

namespace Edde\Node;

use Edde\Edde;
use Iterator;
use RecursiveIterator;
use RecursiveIteratorIterator;

/**
 * Iterator over nodes support with helper classes for recursive iterator, ...
 */
class TreeIterator extends Edde implements RecursiveIterator {
    /** @var ITree */
    protected $tree;
    /** @var Iterator */
    protected $iterator;

    /**
     * The man approached the very beautiful woman in the large supermarket and asked,
     * “You know, I’ve lost my wife here in the supermarket. Can you talk to me for a couple of minutes?”
     * “Why?”
     * “Because every time I talk to a beautiful woman my wife appears out of nowhere.”
     *
     * @param ITree $tree
     */
    public function __construct(ITree $tree) {
        $this->tree = $tree;
    }

    /** @inheritdoc */
    public function next() {
        $this->iterator->next();
    }

    /** @inheritdoc */
    public function key() {
        return $this->iterator->key();
    }

    /** @inheritdoc */
    public function valid() {
        return $this->iterator->valid();
    }

    /** @inheritdoc */
    public function rewind() {
        $this->iterator = $this->tree->getIterator();
        $this->iterator->rewind();
    }

    /** @inheritdoc */
    public function hasChildren() {
        return ($current = $this->current())->isLeaf() === false;
    }

    /** @inheritdoc */
    public function current() {
        return $this->iterator->current();
    }

    /** @inheritdoc */
    public function getChildren() {
        return new self($this->current());
    }

    /**
     * @param ITree $tree
     * @param bool  $isRoot
     *
     * @return RecursiveIteratorIterator|INode[]
     */
    static public function recursive(ITree $tree, bool $isRoot = false): RecursiveIteratorIterator {
        if ($isRoot === true) {
            $root = new Node();
            $root->push($tree);
            $tree = $root;
        }
        $iterator = new RecursiveIteratorIterator(new self($tree), RecursiveIteratorIterator::SELF_FIRST);
        $iterator->rewind();
        return $iterator;
    }
}
