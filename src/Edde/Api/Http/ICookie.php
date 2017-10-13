<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

		interface ICookie {
			/**
			 * return cookie's name
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * a value of the cookie
			 *
			 * @return string
			 */
			public function getValue();

			/**
			 * return a timestamp of expire date or null of not set
			 *
			 * @return int
			 */
			public function getExpire(): ?int;

			/**
			 * defaults to a "/"
			 *
			 * @return string
			 */
			public function getPath(): string;

			/**
			 * domain restriction of cookie
			 *
			 * @return string
			 */
			public function getDomain(): ?string;

			/**
			 * send this cookie only when on a secured connection
			 *
			 * @return bool
			 */
			public function isSecure(): bool;

			/**
			 * cookie is available only in the http protocol (excluding JavaScript, ...)
			 *
			 * @return bool
			 */
			public function isHttpOnly(): bool;

			/**
			 * return cookie data as an array
			 *
			 * @return array
			 */
			public function toArray(): array;
		}
