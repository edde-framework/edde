<?php
	declare(strict_types=1);
	namespace Edde\Element\Converter;

	use Edde\Content\Content;
	use Edde\Content\IContent;
	use Edde\Converter\AbstractConverter;
	use Edde\Converter\ConverterException;
	use Edde\Element\IElement;
	use Edde\Service\Bus\MessageBus;
	use Edde\Service\Converter\ConverterManager;
	use stdClass;

	class JsonDecodeConverter extends AbstractConverter {
		use ConverterManager;
		use MessageBus;

		public function __construct() {
			parent::__construct([
				'application/json',
				'stream://application/json',
			], [
				IElement::class,
			]);
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ConverterException
		 */
		public function convert(IContent $content, string $target = null): IContent {
			$source = $this->converterManager->convert($content, [stdClass::class]);
			return new Content($this->messageBus->import($source->getContent()), IElement::class);
		}
	}
