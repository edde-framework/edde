<?php
	declare(strict_types=1);

	namespace Edde\Common\Resource;

	use Edde\Api\File\FileException;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\ResourceException;
	use Edde\Api\Url\IUrl;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Object;

	/**
	 * Abstract definition of some "resource".
	 */
	class Resource extends Object implements IResource {
		/**
		 * @var IUrl
		 */
		protected $url;
		/**
		 * @var string|null
		 */
		protected $base;
		/**
		 * friendly name of this resource
		 *
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * @param IUrl        $url
		 * @param string|null $base
		 * @param string|null $name
		 * @param string|null $mime
		 */
		public function __construct(IUrl $url, string $base = null, string $name = null, string $mime = null) {
			$this->url = $url;
			$this->base = $base;
			$this->name = $name;
			$this->mime = $mime;
		}

		/**
		 * @inheritdoc
		 */
		public function getUrl() {
			return $this->url;
		}

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 */
		public function getRelativePath(string $base = null) {
			if (($base = $base ?: $this->base) === null) {
				throw new ResourceException(sprintf('Cannot compute relative path of a resource [%s]; there is not base path.', $this->url->getPath()));
			}
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			if (strpos($path = $this->url->getPath(), $base) === false) {
				throw new ResourceException(sprintf('Cannot compute relative path of resource; given base path [%s] is not subset of the current path [%s].', $base, $path));
			}
			return str_replace($base, null, $path);
		}

		/**
		 * @inheritdoc
		 */
		public function getBase() {
			return $this->base;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function getMime(): string {
			if ($this->mime === null) {
				$this->mime = FileUtils::mime($this->url->getAbsoluteUrl());
			}
			return $this->mime;
		}

		/**
		 * @inheritdoc
		 */
		public function isAvailable(): bool {
			return file_exists($url = $this->url->getAbsoluteUrl()) && is_readable($url);
		}

		/**
		 * @inheritdoc
		 */
		public function get() {
			return file_get_contents($this->url->getAbsoluteUrl());
		}

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 */
		public function getIterator() {
			throw new ResourceException(sprintf('Iterator is not supported on raw [%s].', static::class));
		}
	}
