<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;

		abstract class AbstractQuery extends Object implements IQuery {
			/**
			 * @var INode
			 */
			protected $node;

			/**
			 * @inheritdoc
			 */
			public function getParameterList(): array {
				return [];
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				$this->init();
				return $this->node;
			}

			public function setDescription(string $description): AbstractQuery {
				$this->init();
				$this->node->setAttribute('description', $description);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getDescription(): ?string {
				$this->init();
				return $this->node->getAttribute('description', static::class);
			}
		}
