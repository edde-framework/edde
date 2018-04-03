<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Edde;

	class Native extends Edde implements INative {
		protected $query;
		protected $params;

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
