<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereRelation;

		class WhereRelationFragment extends AbstractFragment implements IWhereRelation {
			/**
			 * @var IWhereFragment
			 */
			protected $whereFragment;
			/**
			 * @var string
			 */
			protected $relation;
			/**
			 * @var IWhereFragment
			 */
			protected $where;

			public function __construct(IWhereFragment $whereFragment) {
				$this->whereFragment = $whereFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function and (): IWhereFragment {
				$this->relation = 'and';
				return $this->whereFragment ?: $this->whereFragment = new WhereFragment($this->whereFragment->getSchemaFragment());
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhereFragment {
				$this->relation = 'or';
				return $this->whereFragment ?: $this->whereFragment = new WhereFragment($this->whereFragment->getSchemaFragment());
			}
		}
