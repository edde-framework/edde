<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\IQuery;
		use Edde\Common\Node\Node;

		class WhereInFragment extends AbstractFragment {
			public function values(array $values): WhereRelationFragment {
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('values', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->addNode(new Node('parameter', null, [
					'name'  => $id,
					'value' => $values,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function select(IQuery $query): WhereRelationFragment {
			}
		}
