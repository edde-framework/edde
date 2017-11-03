<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Query\Fragment\ILink;
		use Edde\Api\Schema\IRelation;

		class Link extends SchemaFragment implements ILink {
			/**
			 * @var IRelation
			 */
			protected $relation;
			/**
			 * relation source
			 *
			 * @var array
			 */
			protected $source;

			public function __construct(IRelation $relation, string $alias) {
				parent::__construct($relation->getSchema(), $alias);
				$this->relation = $relation;
				$this->type = 'link';
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
			public function source(array $source): ILink {
				$this->source = $source;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getSource(): array {
				return $this->source;
			}
		}
