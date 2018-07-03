<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Delete;

	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class DeleteQuery extends AbstractQuery {
		/**
		 * @var string
		 */
		protected $source;

		/**
		 * @param string $source
		 */
		public function __construct($source) {
			$this->source = $source;
		}

		protected function prepare() {
			$this->node = new Node('delete-query', $this->source);
		}
	}
