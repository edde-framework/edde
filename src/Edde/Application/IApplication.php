<?php
	declare(strict_types=1);
	namespace Edde\Application;

	/**
	 * Application should connect services for user input translation to
	 * a response, for example router service to protocol service.
	 */
	interface IApplication {
		/**
		 * run the application; if there is any kind of exception, it should NOT be caught as
		 * there could be external logging facility and currently it's considered to be bad practice
		 *
		 * @return int
		 *
		 * @throws ApplicationException
		 */
		public function run(): int;
	}
