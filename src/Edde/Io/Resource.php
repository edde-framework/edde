<?php
	declare(strict_types=1);
	namespace Edde\Io;

	use Edde\Edde;
	use Edde\Url\IUrl;

	/**
	 * Abstract definition of some "resource".
	 */
	class Resource extends Edde implements IResource {
		/** @var IUrl */
		protected $url;
		/** @var string */
		protected $name;

		public function __construct(IUrl $url) {
			$this->url = $url;
		}

		/** @inheritdoc */
		public function getUrl(): IUrl {
			return $this->url;
		}

		/** @inheritdoc */
		public function getPath(): string {
			return $this->url->getPath();
		}

		/** @inheritdoc */
		public function getExtension(): ?string {
			return $this->url->getExtension();
		}

		/** @inheritdoc */
		public function getName(): string {
			return $this->name ?: $this->name = $this->url->getResourceName();
		}

		/** @inheritdoc */
		public function isAvailable(): bool {
			return file_exists($url = $this->url->getAbsoluteUrl()) && is_readable($url);
		}

		/** @inheritdoc */
		public function get(): string {
			return file_get_contents($this->url->getAbsoluteUrl());
		}

		/** @inheritdoc */
		public function getIterator() {
			throw new IoException(sprintf('Iterator is not supported on raw [%s].', static::class));
		}
	}