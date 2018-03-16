<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

	use Edde\Http\IContentType;
	use Edde\Object;

	class ContentType extends Object implements IContentType {
		/**
		 * @var string
		 */
		protected $mime;
		/**
		 * @var string[]
		 */
		protected $parameterList;

		/**
		 * @param string   $mime
		 * @param string[] $parameterList
		 */
		public function __construct(string $mime, array $parameterList = []) {
			$this->mime = $mime;
			$this->parameterList = $parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function getCharset(string $default = 'utf-8'): string {
			return $this->parameterList['charset'] ?? $default;
		}

		/**
		 * @inheritdoc
		 */
		public function getMime(): string {
			return $this->mime;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(): array {
			return $this->parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function __toString(): string {
			return $this->getMime();
		}
	}
