<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;

	class HttpResponseHandler extends AbstractResponseHandler {
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyConverterManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function send(IContent $content): IResponseHandler {
			$convertable = $this->converterManager->content($content, $this->httpRequest->getHeaderList()->getAcceptList());
			$this->httpResponse->setContent($convertable->convert());
			$this->httpResponse->send();
			return $this;
		}
	}
