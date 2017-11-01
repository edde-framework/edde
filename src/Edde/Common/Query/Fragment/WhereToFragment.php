<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereRelation;
		use Edde\Api\Query\Fragment\IWhereTo;

		class WhereToFragment extends AbstractFragment implements IWhereTo {
			/**
			 * @var ISchemaFragment
			 */
			protected $schemaFragment;
			/**
			 * @var string
			 */
			protected $type;
			/**
			 * @var string
			 */
			protected $name;

			public function __construct(ISchemaFragment $schemaFragment, string $type, string $name) {
				$this->schemaFragment = $schemaFragment;
				$this->type = $type;
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function to($value): IWhereRelation {
			}

			/**
			 * @inheritdoc
			 */
			public function toColumn(string $name): IWhereRelation {
			}
		}
