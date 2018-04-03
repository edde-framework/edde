<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Obj3ct;

	abstract class AbstractQuery extends Obj3ct implements IQuery {
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
