<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	use Edde\Api\Bus\Request\IRequest;
	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Api\Router\Exception\RouterException;

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