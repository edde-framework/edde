<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Router is class responsible for handling current application
	 * request (cli, http based, whatever).
	 */
	interface IRouter extends IConfigurable {
		/**
		 * can this router provide IRequest?
		 *
		 * @return bool
		 */
		public function canHandle(): bool;

		/**
		 * create an application request
		 *
		 * @return IRequest
		 */
		public function createRequest(): IRequest;
	}
