<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\IFingerprint;
	use Edde\Common\AbstractObject;

	/**
	 * Don't use session id method.
	 */
	class DummyFingerprint extends AbstractObject implements IFingerprint {
		public function fingerprint() {
			return null;
		}
	}
