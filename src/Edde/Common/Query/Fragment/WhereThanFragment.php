<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Api\Query\Fragment\IWhereThan;
		use Edde\Common\Node\Node;

		class WhereThanFragment extends AbstractFragment implements IWhereThan {
			/**
			 * @inheritdoc
			 */
			public function than($value): IWhereRelation {
				$this->node->setAttribute('target', 'parameter');
				/** @noinspection PhpUnhandledExceptionInspection */
				$this->node->setAttribute('parameter', $id = (sha1(random_bytes(64) . microtime(true))));
				$this->root->getNode('parameter-list')->addNode(new Node('parameter', $value, [
					'name' => $id,
				]));
				return new WhereRelationFragment($this->root, $this->node);
			}

			/**
			 * @inheritdoc
			 */
			public function thanColumn(string $name): IWhereRelation {
				$this->node->setAttribute('target', 'column');
				$this->node->setAttribute('column', $name);
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
