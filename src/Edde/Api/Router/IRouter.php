<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	use Edde\Api\Bus\Request\IRequest;
	use Edde\Config\IConfigurable;
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
		 * @throws \Edde\Exception\Router\BadRequestException
		 * @throws RouterException
		 */
		public function createRequest(): IRequest;
	}
