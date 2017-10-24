<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereIn;
		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Api\Query\ISelectQuery;

		class WhereInFragment extends AbstractFragment implements IWhereIn {
			/**
			 * @inheritdoc
			 */
			public function select(ISelectQuery $selectQuery): IWhereRelation {
				$this->node->setAttribute('target', 'query');
				$this->node->addNode($selectQuery->getQuery());
				return new WhereRelationFragment($this->root, $this->node);
			}
		}
