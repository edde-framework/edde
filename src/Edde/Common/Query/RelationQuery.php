<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\IRelationQuery;
		use Edde\Api\Schema\IRelation;

		/**
		 * General relation query (1:n, m:n).
		 */
		class RelationQuery extends AbstractQuery implements IRelationQuery {
			/**
			 * @var IRelation
			 */
			protected $relation;
			/**
			 * @var array
			 */
			protected $from;
			/**
			 * @var array
			 */
			protected $to;

			public function __construct(IRelation $relation) {
				parent::__construct('relation');
				$this->relation = $relation;
			}

			/**
			 * @inheritdoc
			 */
			public function from(array $source): IRelationQuery {
				$this->from = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function to(array $source): IRelationQuery {
				$this->to = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getRelation(): IRelation {
				return $this->relation;
			}

			/**
			 * @inheritdoc
			 */
			public function getFrom(): array {
				return $this->from;
			}

			/**
			 * @inheritdoc
			 */
			public function getTo(): array {
				return $this->to;
			}
		}
