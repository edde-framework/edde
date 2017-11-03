<?php
	declare(strict_types=1);
	namespace Edde\Common\Query;

		use Edde\Api\Query\ICreateRelationQuery;
		use Edde\Api\Schema\IRelation;

		/**
		 * General relation query (1:n, m:n).
		 */
		class CreateRelationQuery extends AbstractQuery implements ICreateRelationQuery {
			/**
			 * @var IRelation
			 */
			protected $relation;
			/**
			 * @var array|null
			 */
			protected $source;
			/**
			 * @var array
			 */
			protected $from;
			/**
			 * @var array
			 */
			protected $to;

			public function __construct(IRelation $relation, array $source = []) {
				parent::__construct('CreateRelationQuery');
				$this->relation = $relation;
				$this->source = $source;
			}

			/**
			 * @inheritdoc
			 */
			public function hasSource(): bool {
				return empty($this->source) === false;
			}

			/**
			 * @inheritdoc
			 */
			public function getSource(): array {
				return $this->source;
			}

			/**
			 * @inheritdoc
			 */
			public function from(array $source): ICreateRelationQuery {
				$this->from = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function to(array $source): ICreateRelationQuery {
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
