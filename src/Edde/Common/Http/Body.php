<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\IBody;
	use Edde\Common\AbstractObject;

	class Body extends AbstractObject implements IBody, ILazyInject {
		use LazyConverterManagerTrait;
		/**
		 * @var string|callable
		 */
		protected $body;
		/**
		 * @var string
		 */
		protected $mime;
		/**
		 * @var string
		 */
		protected $target;

		public function __construct($body = null, string $mime = null, string $target = null) {
			$this->body = $body;
			$this->mime = $mime;
			$this->target = $target;
		}

		public function convert(string $target = null, string $mime = null) {
			return $this->converterManager->convert($this->getBody(), $mime ?: $this->mime, $this->target ?: $target);
		}

		public function getBody() {
			if (is_callable($this->body)) {
				$this->body = call_user_func($this->body);
			}
			return $this->body;
		}

		public function getMime() {
			return $this->mime;
		}

		public function getTarget() {
			return $this->target;
		}
	}
