<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Edde;

	abstract class AbstractQuery extends Edde implements IQuery {
		/** @var string */
		protected $type;

		/**
		 * @param string $type
		 */
		public function __construct(string $type) {
			$this->type = $type;
		}

		/** @inheritdoc */
		public function getType(): string {
			return $this->type;
		}
	}
