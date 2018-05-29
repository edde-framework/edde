<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\SimpleObject;

	class NativeQuery extends SimpleObject implements INativeQuery {
		/** @var string */
		protected $query;
		/** @var array */
		protected $params;

		/**
		 * @param string $query
		 * @param array  $params
		 */
		public function __construct(string $query, array $params = []) {
			$this->query = $query;
			$this->params = $params;
		}

		/** @inheritdoc */
		public function getQuery(): string {
			return $this->query;
		}

		/** @inheritdoc */
		public function getParams(): array {
			return $this->params;
		}

		/** @inheritdoc */
		public function __toString(): string {
			return $this->getQuery();
		}
	}
