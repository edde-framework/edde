<?php
	declare(strict_types=1);
	namespace Edde\Element\Converter;

	use Edde\Content\Content;
	use Edde\Content\IContent;
	use Edde\Converter\AbstractConverter;
	use Edde\Element\IElement;
	use Edde\Service\Bus\MessageBus;
	use Edde\Service\Converter\ConverterManager;
	use stdClass;

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

		/**
		 * @inheritdoc
		 *
		 * @throws \Edde\Converter\ConverterException
		 */
		public function convert(IContent $content, string $target = null): IContent {
			$source = $this->messageBus->export($content->getContent());
			return $this->converterManager->convert(new Content($source, stdClass::class), ['application/json']);
		}
	}
