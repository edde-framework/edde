<?php
	declare(strict_types=1);
	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Application should connect services for user input translation to
	 * a response, for example router service to protocol service.
	 */
	interface IApplication extends IConfigurable {
		/**
		 * execute the application and return a status code; application should not
		 * die in hard way (thus internally \Throwable should be caught)
		 *
		 * @return int
		 */
		public function run(): int;
	}
