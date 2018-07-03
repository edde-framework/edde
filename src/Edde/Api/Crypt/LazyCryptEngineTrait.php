<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crypt;

	/**
	 * Lazy dependency on a crypt engine.
	 */
	trait LazyCryptEngineTrait {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		/**
		 * @param ICryptEngine $cryptEngine
		 */
		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}
	}
