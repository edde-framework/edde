<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage\Query\Fragment;

	use Edde\Api\Storage\Query\Fragment\IJoin;
	use Edde\Common\Storage\Query\AbstractFragment;

	class Join extends AbstractFragment implements IJoin {
		/**
		 * @var string
		 */
		protected $schema;
		/**
		 * @var string
		 */
		protected $alias;
		/**
		 * @var bool
		 */
		protected $link;

		public function __construct(string $schema, string $alias, bool $link = false) {
			$this->schema = $schema;
			$this->alias = $alias;
			$this->link = $link;
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(): string {
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
		public function isLink(): bool {
			return $this->link;
		}
	}
