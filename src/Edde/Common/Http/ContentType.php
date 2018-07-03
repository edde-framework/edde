<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Common\Collection\AbstractDefferedList;
	use Edde\Common\Deffered\DefferedTrait;

	class ContentType extends AbstractDefferedList implements IContentType {
		use DefferedTrait;
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
			$this->contentType = $contentType;
		}

		public function getCharset(string $default = 'utf-8'): string {
			$this->use();
			return $this->get('charset', $default);
		}

		public function __toString(): string {
			return $this->getMime();
		}

		public function getMime(string $default = ''): string {
			$this->use();
			return $this->object ? $this->object->mime : $default;
		}

		public function getParameterList(): array {
			$this->use();
			return $this->array();
		}

		protected function prepare() {
			if ($this->contentType) {
				$this->object = HttpUtils::contentType($this->contentType);
				$this->put($this->object->params);
			}
		}
	}
