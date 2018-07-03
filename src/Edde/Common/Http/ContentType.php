<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Common\Collection\AbstractList;

	class ContentType extends AbstractList implements IContentType {
		/**
		 * source content type
		 *
		 * @var string
		 */
		protected $contentType;
		/**
		 * parsed content type
		 *
		 * @var \stdClass
		 */
		protected $object;

		/**
		 * ContentType constructor.
		 *
		 * @param string $contentType can be only content type part or whole content type header
		 */
		public function __construct(string $contentType) {
			parent::__construct();
			if ($this->contentType = $contentType) {
				$this->object = HttpUtils::contentType($this->contentType);
				$this->put($this->object->params);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getCharset(string $default = 'utf-8'): string {
			return (string)$this->get('charset', $default);
		}

		/**
		 * @inheritdoc
		 */
		public function getMime(string $default = null) {
			return $this->object ? $this->object->mime : $default;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(): array {
			return $this->array();
		}

		/**
		 * @inheritdoc
		 */
		public function __toString(): string {
			return $this->getMime();
		}
	}
