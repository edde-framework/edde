<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Common\Node\Node;

		class WhereThanFragment extends AbstractFragment {
			public function than($value): WhereRelationFragment {
				$this->node->setAttribute('type', 'than');
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('parameter', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->addNode(new Node('parameter', null, [
					'name'  => $id,
					'value' => $value,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function thanColumn(string $name): WhereRelationFragment {
				$this->node->setAttribute('type', 'than-column');
				$this->node->setAttribute('column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
