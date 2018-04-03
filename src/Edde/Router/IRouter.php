<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Container\IAutowire;
	use Edde\Element\IRequest;

	/**
	 * Router is class responsible for handling current application
	 * request (cli, http based, whatever).
	 */
	interface IRouter extends IAutowire {
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
		 *
		 * @throws RouterException
		 */
		public function createRequest(): IRequest;
	}
