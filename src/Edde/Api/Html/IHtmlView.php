<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;

	/**
	 * Formal root implementation for a html page/fragment.
	 */
	interface IHtmlView extends IHtmlControl {
		/**
		 * helper method for html control creation
		 *
		 * @param string $control
		 * @param array ...$parameterList
		 *
		 * @return IHtmlControl|IControl
		 */
		public function createControl(string $control, ...$parameterList): IControl;

		/**
		 * send response to the current request
		 */
		public function response();

		/**
		 * @param $redirect
		 */
		public function redirect($redirect);
	}
