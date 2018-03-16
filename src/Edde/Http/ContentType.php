<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Object;

	class ContentType extends Object implements IContentType {
		/** @var string */
		protected $mime;
		/** @var string[] */
		protected $params;

		/**
		 * @param string   $mime
		 * @param string[] $params
		 */
		public function __construct(string $mime, array $params = []) {
			$this->mime = $mime;
			$this->params = $params;
		}

		/** @inheritdoc */
		public function getCharset(string $default = 'utf-8'): string {
			return $this->params['charset'] ?? $default;
		}

		/** @inheritdoc */
		public function getMime(): string {
			return $this->mime;
		}

		/** @inheritdoc */
		public function getParameters(): array {
			return $this->params;
		}

		/** @inheritdoc */
		public function __toString(): string {
			return $this->getMime();
		}
	}
