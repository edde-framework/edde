<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\IQuery;
		use Edde\Common\Node\Node;

		class WhereInFragment extends AbstractFragment {
			public function values(array $values): WhereRelationFragment {
				$this->node->setAttribute('target', 'parameter');
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('parameter', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->getNode('parameter-list')->addNode(new Node('parameter', $values, [
					'name' => $id,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			public function select(IQuery $query): WhereRelationFragment {
				$this->node->setAttribute('target', 'query');
				$this->node->addNode($query->getQuery());
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
