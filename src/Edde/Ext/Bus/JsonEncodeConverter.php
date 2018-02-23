<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\Inject\MessageBus;
	use Edde\Api\Content\IContent;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Common\Content\Content;
	use Edde\Common\Converter\AbstractConverter;

	class JsonEncodeConverter extends AbstractConverter {
		use ConverterManager;
		use MessageBus;

		public function __construct() {
			parent::__construct([
				IElement::class,
			], [
				'application/json',
			]);
		}

		/** @inheritdoc */
		public function convert(IContent $content, string $target = null): IContent {
			$source = $this->messageBus->export($content->getContent());
			return $this->converterManager->convert(new Content($source, \stdClass::class), ['application/json']);
		}
	}