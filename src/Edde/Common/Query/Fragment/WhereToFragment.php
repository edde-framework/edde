<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereToFragment extends AbstractFragment {
			public function to($value): WhereRelationFragment {
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('to', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->addNode(new Node('parameter', null, [
					'name'  => $id,
					'value' => $value,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function toColumn(string $name): WhereRelationFragment {
				$this->node->setAttribute('to-column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
