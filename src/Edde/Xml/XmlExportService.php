<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Edde;
	use Edde\Io\IFile;
	use Edde\Node\INode;
	use Iterator;
	use SplStack;

	class XmlExportService extends Edde implements IXmlExportService {
		/** @inheritdoc */
		public function node(Iterator $iterator): void {
			$stack = new SplStack();
			$level = -1;
			/** @var $node INode */
			foreach ($iterator as $node) {
				$value = null;
				if ($node->getLevel() < $level) {
					echo $stack->pop();
				}
				$indentation = str_repeat("\t", $node->getLevel());
				$metaList = $node->getMetas();
				$isClosed = (($value = $node->getValue()) === null) && ($node->isLeaf() || $metaList->get('pair', false));
				$close = '</' . $node->getName() . ">\n";
				if ($isClosed === false && ($node->isLeaf() === false || $value === null)) {
					$stack->push($indentation . $close);
				}
				$content = [];
				$content[] = $indentation . '<' . $node->getName();
				foreach ($node->getAttributes() as $name => $list) {
					$content[] = ' ' . $name . '="' . htmlspecialchars((string)$list, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '"';
				}
				if ($isClosed) {
					$content[] = '/';
				}
				$content[] = '>' . (($value && $node->isLeaf()) ? '' : "\n");
				echo implode('', $content);
				if ($value && $node->isLeaf()) {
					echo $value;
				}
				if ($isClosed === false && $value && $node->isLeaf()) {
					echo $close;
				}
				$level = $node->getLevel();
			}
			while ($stack->isEmpty() === false) {
				echo $stack->pop();
			}
		}

		/** @inheritdoc */
		public function export(Iterator $iterator, IFile $file): IFile {
			$file->openForWrite();
			$file->write($this->string($iterator));
			$file->close();
			return $file;
		}

		/** @inheritdoc */
		public function string(Iterator $iterator): string {
			ob_start();
			$this->node($iterator);
			return ob_get_clean();
		}
	}
