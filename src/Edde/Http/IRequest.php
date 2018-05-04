<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Url\IUrl;

	/**
	 * Low level implementation of HTTP request.
	 */
	interface IRequest extends IHttp {
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
		 * @return IUrl
		 */
		public function getUrl(): IUrl;

		/**
		 * @return bool
		 */
		public function isSecured(): bool;
	}
