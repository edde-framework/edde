<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Config\IConfigurable;
	use Edde\Url\IUrl;

	interface IRequestService extends IConfigurable {
		/**
		 * return a singleton instance representing current http request
		 *
		 * @return IRequest
		 */
		public function getRequest(): IRequest;

		/**
		 * try to get content with the acceptable list of targets (like array, object, ...)
		 *
		 * @param array ...$targets
		 *
		 * @return mixed
		 *
		 * @throws EmptyBodyException
		 */
		public function getContent(...$targets);

		/**
		 * get current request url
		 *
		 * @return IUrl
		 */
		public function getUrl(): IUrl;

		/**
		 * return current uppercase http method
		 *
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * get headers of the current request
		 *
		 * @return IHeaders
		 */
		public function getHeaders(): IHeaders;

		/**
		 * return current content type if it's known
		 *
		 * @return IContentType|null
		 */
		public function getContentType(): ?IContentType;
	}
