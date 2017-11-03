<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\IWhereExpression;
		use Edde\Api\Query\Fragment\IWhereGroup;

		class WhereExpression extends AbstractFragment implements IWhereExpression {
			/**
			 * @var IWhereGroup
			 */
			protected $whereGroup;
			/**
			 * @var string
			 */
			protected $operator;
			/**
			 * @var string
			 */
			protected $name;
			protected $alias;
			/**
			 * target of an expression
			 *
			 * @var string
			 */
			protected $target;
			protected $value;

			public function __construct(IWhereGroup $whereGroup, string $operator, string $name, string $alias = null) {
				parent::__construct('where-expression-' . $operator);
				$this->whereGroup = $whereGroup;
				$this->operator = $operator;
				$this->name = $name;
				$this->alias = $alias;
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return $this->name;
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): ?string {
				return $this->alias;
			}

			/**
			 * @inheritdoc
			 */
			public function getTarget(): string {
				return $this->target;
			}

			/**
			 * @inheritdoc
			 */
			public function getValue() {
				return $this->value;
			}
		}
