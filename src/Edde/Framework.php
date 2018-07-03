<?php
	declare(strict_types=1);

	namespace Edde;

	use Edde\Common\Object;

	/**
	 * Information about framework hidden in this class.
	 */
	class Framework extends Object {
		/**
		 * return full version string
		 *
		 * @return string
		 */
		public function getVersionString() {
			return $this->getVersion() . ' - ' . $this->getCodename();
		}

		/**
		 * return current version of framework
		 *
		 * @return string
		 */
		public function getVersion() {
			return '3.0.1.597';
		}

		/**
		 * return current codename of framework
		 *
		 * @return string
		 */
		public function getCodename() {
			return 'The Unepic Epicness';
		}

		public function __toString() {
			return $this->getVersionString();
		}
	}
