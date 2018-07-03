<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	interface ICookie {
		/**
		 * return cookie's name
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * a value of the cookie
		 *
		 * @return string
		 */
		public function getValue();

		/**
		 * @return int
		 */
		public function getExpire();

		/**
		 * defaults to a "/"
		 *
		 * @return string
		 */
		public function getPath();

		/**
		 * domain restriction of cookie
		 *
		 * @return string
		 */
		public function getDomain();

		/**
		 * send this cookie only when on a secured connection
		 *
		 * @return bool
		 */
		public function isSecure();

		/**
		 * cookie is available only in the http protocol (excluding JavaScript, ...)
		 *
		 * @return bool
		 */
		public function isHttpOnly();
	}
