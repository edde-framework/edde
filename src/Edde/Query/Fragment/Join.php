<?php
	declare(strict_types=1);
	namespace Edde\Query\Fragment;

	use Edde\Query\AbstractFragment;
	use Edde\Storage\Query\Fragment\IJoin;

	class Join extends AbstractFragment implements IJoin {
		/** @var string */
		protected $schema;
		/** @var string */
		protected $alias;
		/** @var bool */
		protected $link;
		/** @var string */
		protected $relation;

		public function __construct(string $schema, string $alias, bool $link = false, string $relation = null) {
			$this->schema = $schema;
			$this->alias = $alias;
			$this->link = $link;
			$this->relation = $relation;
		}

		/** @inheritdoc */
		public function getSchema(): string {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getAlias(): string {
			return $this->alias;
		}

		/** @inheritdoc */
		public function isLink(): bool {
			return $this->link;
		}

		/** @inheritdoc */
		public function getRelation(): ?string {
			return $this->relation;
		}
	}