<?php
	namespace Edde\Common\Query;

		class RelationQuery extends AbstractQuery {
			/**
			 * @var string
			 */
			protected $relation;

			/**
			 * @param string $relation
			 */
			public function __construct(string $relation) {
				$this->relation = $relation;
			}

			public function addRelation(string $schema, string $property, string $value): RelationQuery {
				return $this;
			}
		}
