<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Content\IContent;
	use Edde\Common\Content\Content;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Exception\Converter\ConverterException;
	use Edde\Inject\Bus\MessageBus;
	use Edde\Inject\Converter\ConverterManager;
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
