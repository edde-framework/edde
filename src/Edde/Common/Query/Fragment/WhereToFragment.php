<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereToFragment extends AbstractFragment {
			public function to($value): WhereRelationFragment {
				$this->node->setAttribute('type', 'to');
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('parameter', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->addNode(new Node('parameter', null, [
					'name'  => $id,
					'value' => $value,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function toColumn(string $name): WhereRelationFragment {
				$this->node->setAttribute('type', 'to-column');
				$this->node->setAttribute('column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
