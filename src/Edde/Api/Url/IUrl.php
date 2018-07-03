<?php
	declare(strict_types=1);

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
		 * @param bool $query
		 *
		 * @return string
		 */
		public function getPath(bool $query = true);

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
		 * @param string $query
		 *
		 * @return IUrl
		 */
		public function setQuery(string $query): IUrl;

		/**
		 * @return string
		 */
		public function getQuery();

		/**
		 * @return array
		 */
		public function getParameterList(): array;

		/**
		 * @param array $parameterList
		 *
		 * @return IUrl
		 */
		public function setParameterList(array $parameterList): IUrl;

		/**
		 * @param array $parameterList
		 *
		 * @return IUrl
		 */
		public function addParameterList(array $parameterList): IUrl;

		/**
		 * update the given parameter
		 *
		 * @param string $name
		 * @param        $value
		 *
		 * @return IUrl
		 */
		public function setParameter(string $name, $value): IUrl;

		/**
		 * @param string      $name
		 * @param string|null $default
		 *
		 * @return string
		 */
		public function getParameter(string $name, $default = null);

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
		public function parse(string $url);

		/**
		 * run regular expression against absolute url
		 *
		 * @param string $match
		 * @param bool   $path === true, match only path
		 *
		 * @return array|null
		 */
		public function match(string $match, bool $path = true);

		public function __toString();
	}
