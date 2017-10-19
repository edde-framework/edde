<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereThanFragment extends AbstractFragment {
			public function than($value): WhereRelationFragment {
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('than', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->addNode(new Node('parameter', null, [
					'name'  => $id,
					'value' => $value,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function thanColumn(string $name): WhereRelationFragment {
				$this->node->setAttribute('than-column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
