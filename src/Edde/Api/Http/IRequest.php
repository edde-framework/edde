<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

	use Edde\Url\IUrl;

	/**
	 * Low level implementation of HTTP request.
	 */
	interface IRequest extends IHttp {
		/**
		 * set the request method
		 *
		 * @param string $method
		 *
		 * @return IRequest
		 */
		public function setMethod(string $method): IRequest;

		/**
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * @param string $method
		 *
		 * @return bool
		 */
		public function isMethod(string $method): bool;

		/**
		 * @return null|string
		 */
		public function getRemoteAddress(): ?string;

		/**
		 * @return null|string
		 */
		public function getRemoteHost(): ?string;

		/**
		 * @return IUrl
		 */
		public function getUrl(): IUrl;

		/**
		 * @return IUrl|null
		 */
		public function getReferer(): ?IUrl;

		/**
		 * @return bool
		 */
		public function isSecured(): bool;
	}
