<?php
	declare(strict_types=1);

	namespace Edde\Api\Session;

	/**
	 * LAzy fingerprint dependency.
	 */
	trait LazyFingerprintTrait {
		/**
		 * @var IFingerprint
		 */
		protected $fingerprint;

		/**
		 * @param IFingerprint $fingerprint
		 */
		public function lazyFingerprint(IFingerprint $fingerprint) {
			$this->fingerprint = $fingerprint;
		}
	}
