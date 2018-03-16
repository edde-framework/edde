<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Config\IConfigurable;
	use Edde\Exception\Http\EmptyBodyException;
	use Edde\Exception\Http\NoHttpException;
	use Edde\Url\IUrl;

	interface IRequestService extends IConfigurable {
		/**
		 * return a singleton instance representing current http request
		 *
		 * @return IRequest
		 *
		 * @throws NoHttpException
		 */
		public function getRequest(): IRequest;

		/**
		 * try to get content with the acceptable list of targets (like array, object, ...)
		 *
		 * @param array ...$targetList
		 *
		 * @return mixed
		 * @throws EmptyBodyException
		 */
		public function getContent(...$targetList);

		/**
		 * get current request url
		 *
		 * @return IUrl
		 *
		 * @throws NoHttpException
		 */
		public function getUrl(): IUrl;

		/**
		 * return current uppercase http method
		 *
		 * @return string
		 *
		 * @throws NoHttpException
		 */
		public function getMethod(): string;

		/**
		 * get headers of the current request
		 *
		 * @return IHeaders
		 *
		 * @throws NoHttpException
		 */
		public function getHeaders(): IHeaders;

		/**
		 * return current content type if it's known
		 *
		 * @return IContentType|null
		 *
		 * @throws NoHttpException
		 */
		public function getContentType(): ?IContentType;
	}
