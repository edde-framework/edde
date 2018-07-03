<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Config\ConfigurableTrait;

	class ResponseManager extends AbstractResponseHandler implements IResponseManager {
		use LazyConverterManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var IContent
		 */
		protected $response;
		/**
		 * @var IResponseHandler
		 */
		protected $responseHandler;

		/**
		 * @inheritdoc
		 */
		public function setResponseHandler(IResponseHandler $responseHandler = null): IResponseManager {
			$this->responseHandler = $responseHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasResponse(): bool {
			return $this->response !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function response(IContent $content): IResponseManager {
			$this->response = $content;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContent $content = null) {
			if ($this->response === null) {
				return;
			}
			$this->responseHandler = $this->responseHandler ?: $this;
			$this->responseHandler->setup();
			$this->responseHandler->send($this->response);
		}

		/**
		 * @inheritdoc
		 */
		public function send(IContent $content): IResponseHandler {
			throw new UnknownResponseHandlerException('There is no response handler to process current response.');
		}
	}
