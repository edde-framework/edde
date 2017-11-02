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
			/**
			 * target of an expression
			 *
			 * @var string
			 */
			protected $target;
			protected $value;

			public function __construct(IWhereGroup $whereGroup, string $operator, string $name) {
				parent::__construct('where-expression-' . $operator);
				$this->whereGroup = $whereGroup;
				$this->operator = $operator;
				$this->name = $name;
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
