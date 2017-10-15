<?php
	declare(strict_types=1);
	namespace Edde\Common\Resource;

		use Edde\Api\Resource\Exception\ResourceException;
		use Edde\Api\Resource\IResource;
		use Edde\Api\Url\IUrl;
		use Edde\Common\Object\Object;
		use Edde\Common\Url\Url;

		/**
		 * Abstract definition of some "resource".
		 */
		class Resource extends Object implements IResource {
			/**
			 * @var IUrl
			 */
			protected $url;
			/**
			 * friendly name of this resource
			 *
			 * @var string
			 */
			protected $name;

			/**
			 * @param string      $url
			 * @param string|null $name
			 */
			public function __construct(string $url, string $name = null) {
				$this->url = $url;
				$this->name = $name;
			}

			/**
			 * @inheritdoc
			 */
			public function getUrl(): IUrl {
				return $this->url instanceof IUrl ? $this->url : Url::create($this->url);
			}

			/**
			 * @inheritdoc
			 */
			public function getPath(): string {
				return $this->url->getPath();
			}

			/**
			 * @inheritdoc
			 */
			public function getExtension(): ?string {
				return $this->url->getExtension();
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return $this->name ?: $this->name = $this->url->getResourceName();
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
			public function get(): string {
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
