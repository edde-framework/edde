<?php
	declare(strict_types = 1);

	namespace Edde\Api\Url;

	interface IUrl {
		/**
		 * @return string
		 */
		public function getScheme();

		/**
		 * @return string
		 */
		public function getUser();

		/**
		 * @return string
		 */
		public function getPassword();

		/**
		 * @return string
		 */
		public function getHost();

		/**
		 * @return int
		 */
		public function getPort();

		/**
		 * set path path of an url
		 *
		 * @param string $path
		 *
		 * @return IUrl
		 */
		public function setPath(string $path): IUrl;

		/**
		 * @return string
		 */
		public function getPath();

		/**
		 * @return string[]
		 */
		public function getPathList();

		/**
		 * return path without filename (simply throw away last part of url)
		 *
		 * @return string
		 */
		public function getBasePath(): string;

		/**
		 * return last part of path, if available, as resource name (commonly filename)
		 *
		 * @return string
		 */
		public function getResourceName();

		/**
		 * a little tricky method - return extension (.somthing), if it is present in url
		 *
		 * @return string|null
		 */
		public function getExtension();

		/**
		 * set query part of an url
		 *
		 * @param array $query
		 *
		 * @return IUrl
		 */
		public function setQuery(array $query): IUrl;

		/**
		 * @return array
		 */
		public function getQuery();

		/**
		 * @param string $name
		 * @param string|null $default
		 *
		 * @return string
		 */
		public function getParameter($name, $default = null);

		/**
		 * @return string
		 */
		public function getFragment();

		/**
		 * @return string
		 */
		public function getAbsoluteUrl(): string;

		/**
		 * @param string $url
		 *
		 * @return $this
		 */
		public function build($url);

		public function __toString();
	}
