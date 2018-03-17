<?php
	declare(strict_types=1);
	namespace Edde\Url;

	use Edde\Object;

	class Url extends Object implements IUrl {
		/** @var string */
		protected $scheme = '';
		/** @var string */
		protected $user = '';
		/** @var string */
		protected $password = '';
		/** @var string */
		protected $host = '';
		/** @var int */
		protected $port;
		/** @var string */
		protected $path = '';
		/** @var string */
		protected $query;
		/** @var array */
		protected $params = [];
		/** @var string */
		protected $fragment = '';

		public function __construct($url = null) {
			$url ? $this->parse((string)$url) : null;
		}

		/** @inheritdoc */
		public function parse(string $url) {
			if (($parsed = parse_url($url)) === false) {
				throw new UrlException(sprintf('Malformed URL [%s].', $url));
			}
			if (isset($parsed['query'])) {
				parse_str($parsed['query'], $parsed['parameter-list']);
			}
			static $copy = [
				'scheme'         => 'setScheme',
				'user'           => 'setUser',
				'pass'           => 'setPassword',
				'host'           => 'setHost',
				'port'           => 'setPort',
				'path'           => 'setPath',
				'query'          => 'setQuery',
				'parameter-list' => 'setParameterList',
				'fragment'       => 'setFragment',
			];
			foreach ($copy as $item => $func) {
				if (isset($parsed[$item])) {
					$this->{$func}($parsed[$item]);
				}
			}
			return $this;
		}

		/** @inheritdoc */
		public function getResourceName(): string {
			$pathList = $this->getPathList();
			return (string)end($pathList);
		}

		/** @inheritdoc */
		public function getPathList() {
			return explode('/', ltrim($this->path, '/'));
		}

		/** @inheritdoc */
		public function getBasePath(): string {
			$pathList = $this->getPathList();
			array_pop($pathList);
			return implode('/', $pathList);
		}

		/** @inheritdoc */
		public function getExtension(): ?string {
			$path = $this->getPath();
			$subpath = substr($path, strrpos($path, '/'));
			if (($index = strrpos($subpath, '.')) === false) {
				return null;
			}
			return substr($subpath, $index + 1);
		}

		/** @inheritdoc */
		public function getPath(bool $query = true) {
			return $this->path . ($query && empty($this->params) === false ? '?' . http_build_query($this->params) : '');
		}

		/** @inheritdoc */
		public function setPath(string $path): \Edde\Url\IUrl {
			$this->path = $path;
			return $this;
		}

		/** @inheritdoc */
		public function getAbsoluteUrl(): string {
			$scheme = $this->getScheme();
			$url = '';
			if ($scheme !== '') {
				$url = $scheme . '://';
			}
			if (($user = $this->getUser()) !== '') {
				$url .= $user;
				if (($password = $this->getPassword()) !== '') {
					$url .= ':' . $password;
				}
				$url .= '@';
			}
			$url .= ($host = $this->getHost());
			if ($host !== '' && ($port = $this->getPort()) !== null) {
				$url .= ':' . $port;
			}
			$url .= '/' . ltrim($this->getPath(false), '/');
			$query = $this->getParameterList();
			if (empty($query) === false) {
				$url .= '?' . http_build_query($query);
			}
			if (($fragment = $this->getFragment()) !== '') {
				$url .= '#' . $fragment;
			}
			return $url;
		}

		/** @inheritdoc */
		public function getScheme() {
			return $this->scheme;
		}

		/** @inheritdoc */
		public function setScheme($scheme) {
			$this->scheme = $scheme;
			return $this;
		}

		/** @inheritdoc */
		public function getUser() {
			return $this->user;
		}

		/** @inheritdoc */
		public function setUser($user) {
			$this->user = $user;
			return $this;
		}

		/** @inheritdoc */
		public function getPassword() {
			return $this->password;
		}

		/** @inheritdoc */
		public function setPassword($password) {
			$this->password = $password;
			return $this;
		}

		/** @inheritdoc */
		public function getHost() {
			return $this->host;
		}

		/** @inheritdoc */
		public function setHost($host) {
			$this->host = $host;
			return $this;
		}

		/** @inheritdoc */
		public function getPort() {
			return $this->port;
		}

		/** @inheritdoc */
		public function setPort($port) {
			$this->port = $port;
			return $this;
		}

		/** @inheritdoc */
		public function getQuery() {
			return $this->query;
		}

		/** @inheritdoc */
		public function setQuery(string $query): IUrl {
			$this->query = $query;
			return $this;
		}

		/** @inheritdoc */
		public function setParams(array $params): IUrl {
			$this->params = $params;
			return $this;
		}

		/** @inheritdoc */
		public function addParams(array $params): IUrl {
			$this->params = array_merge($this->params, $params);
			return $this;
		}

		/** @inheritdoc */
		public function getParameterList(): array {
			return $this->params;
		}

		/** @inheritdoc */
		public function setParameter(string $name, $value): IUrl {
			$this->params[$name] = $value;
			return $this;
		}

		/** @inheritdoc */
		public function getFragment() {
			return $this->fragment;
		}

		/** @inheritdoc */
		public function setFragment($fragment) {
			$this->fragment = $fragment;
			return $this;
		}

		/** @inheritdoc */
		public function getParameter(string $name, $default = null) {
			if (isset($this->params[$name]) === false) {
				return $default;
			}
			return $this->params[$name];
		}

		/** @inheritdoc */
		public function match(string $match, bool $path = true) {
			return preg_match($match, $path ? $this->getPath(false) : $this->getAbsoluteUrl());
		}

		/** @inheritdoc */
		public function __toString() {
			return $this->getAbsoluteUrl();
		}

		/**
		 * create a "clone" of the givne url
		 *
		 * @param null $url
		 *
		 * @return IUrl|$this
		 */
		static public function create($url = null) {
			return new static($url);
		}
	}
