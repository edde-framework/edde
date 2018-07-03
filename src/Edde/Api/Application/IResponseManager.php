<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Converter\IContent;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IResponseHandler {
		/**
		 * who will take care about response when execute() is called? If null is provided, response manager itself will hold
		 * basic output
		 *
		 * @param IResponseHandler $responseHandler
		 *
		 * @return IResponseManager
		 */
		public function setResponseHandler(IResponseHandler $responseHandler = null): IResponseManager;

		/**
		 * is there already some response?
		 *
		 * @return bool
		 */
		public function hasResponse(): bool;

		/**
		 * set the current response
		 *
		 * @param IContent $content
		 *
		 * @return IResponseManager
		 */
		public function response(IContent $content): IResponseManager;

		/**
		 * execute response
		 *
		 * @param IContent|null $content
		 *
		 * @return mixed
		 */
		public function execute(IContent $content = null);
	}
