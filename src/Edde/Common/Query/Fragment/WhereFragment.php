<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereFragment;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereFragment extends AbstractFragment implements IWhereFragment {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;
			/**
			 * @var IWhereTo
			 */
			protected $whereTo;

			public function __construct(ISchemaFragment $schemaFragment) {
				$this->schemaFragment = $schemaFragment;
			}

			/**
			 * @inheritdoc
			 */
			public function eq(string $name): IWhereTo {
				return $this->whereTo ?: $this->whereTo = new WhereToFragment($this, __FUNCTION__, $name);
			}

			/**
			 * @inheritdoc
			 */
			public function group(): IWhereFragment {
				return new WhereFragment($this->schemaFragment);
			}

			/**
			 * @inheritdoc
			 */
			public function and (): IWhereFragment {
				return (new WhereRelationFragment($this))->and();
			}

			/**
			 * @inheritdoc
			 */
			public function or (): IWhereFragment {
				return (new WhereRelationFragment($this))->or();
			}

			/**
			 * @inheritdoc
			 */
			public function getSchemaFragment(): ISchemaFragment {
				return $this->schemaFragment;
			}
		}
