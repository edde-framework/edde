<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ISchemaFragment;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;

		class SchemaFragment extends AbstractFragment implements ISchemaFragment {
			/**
			 * @var ISchema
			 */
			protected $schema;
			/**
			 * @var string
			 */
			protected $alias;
			/**
			 * @var bool
			 */
			protected $selected = false;
			/**
			 * @var IWhereGroup
			 */
			protected $where;
			/**
			 * @var ISchemaFragment[]
			 */
			protected $relationList = [];

			public function __construct(ISchema $schema, string $alias) {
				parent::__construct('schema');
				$this->schema = $schema;
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function select(): ISchemaFragment {
				$this->selected = true;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isSelected(): bool {
				return $this->selected;
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				return $this->schema;
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): string {
				return $this->alias;
			}

			/**
			 * @inheritdoc
			 */
			public function hasWhere(): bool {
				return $this->where !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function where(): IWhereGroup {
				if ($this->where === null) {
					$this->where = new WhereGroup($this);
				}
				return $this->where;
			}

			/**
			 * @inheritdoc
			 */
			public function relation(IRelation $relation, array $source, string $alias): ISchemaFragment {
				$schemaFragment = new SchemaFragment($relation->getSchema(), $alias);
				return $schemaFragment;
			}
		}
