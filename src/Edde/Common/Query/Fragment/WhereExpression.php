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

			public function __construct(IWhereGroup $whereGroup, string $operator, string $name) {
				$this->whereGroup = $whereGroup;
				$this->operator = $operator;
				$this->name = $name;
			}
		}
