<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	/**
	 * General response (result) from an application. It can be handled by an arbitrary service.
	 */
	interface IResponse {
		/**
		 * "mime" type of the response; can be arbitrary string (for example class name)
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * response can be arbitrary (object, callable, ...)
		 *
		 * @return mixed
		 */
		public function getResponse();
	}
