<?php
	declare(strict_types=1);

	namespace Edde\Api\Session;

	/**
	 * Interface intended to implement user detection (session id generator).
	 */
	interface IFingerprint {
		/**
		 * generate user's fingerprint (session id)
		 *
		 * @return string|null
		 */
		public function fingerprint();
	}
