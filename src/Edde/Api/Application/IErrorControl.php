<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Control\IControl;

	interface IErrorControl extends IControl {
		/**
		 * when application crashes, this control should handle failature situation
		 *
		 * @param \Exception $e
		 */
		public function exception(\Exception $e);
	}
