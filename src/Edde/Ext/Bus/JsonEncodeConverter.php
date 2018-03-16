<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus;

	use Edde\Common\Converter\AbstractConverter;
	use Edde\Content\Content;
	use Edde\Content\IContent;
	use Edde\Inject\Bus\MessageBus;
	use Edde\Inject\Converter\ConverterManager;
	use stdClass;

	class JsonEncodeConverter extends AbstractConverter {
		use ConverterManager;
		use MessageBus;

		public function __construct() {
			parent::__construct([
				\Edde\Element\IElement::class,
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
