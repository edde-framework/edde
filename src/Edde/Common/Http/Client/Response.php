<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\Client\IResponse;
	use Edde\Common\Http\Response as HttpResponse;

	class Response extends HttpResponse implements IResponse {
		use LazyConverterManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function convert(array $targetList): IContent {
			return $this->converterManager->content($this->getContent(), $targetList)->convert();
		}
	}
