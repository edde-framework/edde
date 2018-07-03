<?php
	declare(strict_types = 1);

	namespace Edde\Api\Deffered;

	/**
	 * Any class can be usable in terms of passive behavior until "touch". This was originally developed from Service interface.
	 */
	interface IDeffered {
		/**
		 * callback executed before {@see self::use()}
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onDeffered(callable $callback);

		/**
		 * callback executed after {@see self::use()}
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onSetup(callable $callback);

		/**
		 * general purpose callback executed when usable is loaded (used); if it is already loaded, callback is executed immediately
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onLoaded(callable $callback);

		/**
		 * prepare for the first usage
		 *
		 * @return $this
		 */
		public function use ();

		/**
		 * has been deffered already used (prepared)?
		 *
		 * @return bool
		 */
		public function isUsed();
	}
