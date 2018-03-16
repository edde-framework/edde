<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Config\IConfigurable;
	use Edde\Element\IRequest;
	use Edde\Exception\Router\BadRequestException;
	use Edde\Exception\Router\RouterException;

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
		 *
		 * @throws BadRequestException
		 * @throws RouterException
		 */
		public function createRequest(): IRequest;
	}
