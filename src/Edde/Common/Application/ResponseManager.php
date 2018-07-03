<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Deffered\AbstractDeffered;

	class ResponseManager extends AbstractDeffered implements IResponseManager {
		use LazyConverterManagerTrait;
		use LazyRequestTrait;
		/**
		 * @var IResponse
		 */
		protected $response;
		/**
		 * @var string
		 */
		protected $mime;

		public function response(IResponse $response): IResponseManager {
			$this->response = $response;
			return $this;
		}

		public function getMime(): string {
			return $this->mime;
		}

		public function setMime(string $mime): IResponseManager {
			$this->mime = $mime;
			return $this;
		}

		public function execute() {
			if ($this->response === null) {
				return;
			}
			$this->use();
			$this->converterManager->convert($this->response->getResponse(), $this->response->getType(), $this->mime);
		}
	}
