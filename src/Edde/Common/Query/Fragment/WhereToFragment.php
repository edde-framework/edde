<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereToFragment extends AbstractFragment {
			public function to($value): WhereRelationFragment {
				$this->node->setAttribute('target', 'parameter');
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('parameter', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->getNode('parameter-list')->addNode(new Node('parameter', $value, [
					'name' => $id,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function toColumn(string $name): WhereRelationFragment {
				$this->node->setAttribute('target', 'column');
				$this->node->setAttribute('column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
