<?php
	declare(strict_types=1);

	namespace Edde\Api\Crypt\Inject;

	use Edde\Api\Crypt\ICryptEngine;

	/**
	 * Lazy dependency on a crypt engine.
	 */
	trait CryptEngine {
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
