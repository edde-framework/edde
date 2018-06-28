<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Configurable\IConfigurable;
	use Edde\Content\IContent;
	use Edde\Url\IUrl;

	interface IRequestService extends IConfigurable {
		/**
		 * return a singleton instance representing current http request
		 *
		 * @return IRequest
		 */
		public function getRequest(): IRequest;

		/**
		 * return content from a request
		 *
		 * @return IContent
		 *
		 * @throws EmptyBodyException
		 */
		public function getContent(): IContent;

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
	}
