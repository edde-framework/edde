<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhere;
		use Edde\Api\Query\Fragment\IWhereGroup;

		class WhereGroup extends AbstractFragment implements IWhereGroup {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;
			/**
			 * @var IWhere[]
			 */
			protected $whereList = [];

			public function __construct(ISchemaFragment $schemaFragment) {
				parent::__construct('where-group');
				$this->schemaFragment = $schemaFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function and (): IWhere {
				return $this->whereList[] = new Where($this, 'and');
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhere {
				return $this->whereList[] = new Where($this, 'or');
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragment(): ISchemaFragment {
				return $this->schemaFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				yield from $this->whereList;
			}
		}
