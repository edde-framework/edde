<?php
	declare(strict_types=1);
	namespace Edde\Ext\Converter;

	use Edde\Api\Content\IContent;
	use Edde\Common\Content\Content;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Exception\Converter\UnsupportedConversionException;

	class JsonDecodeConverter extends AbstractConverter {
		public function __construct() {
			parent::__construct([
				'application/json',
				'stream://application/json',
			], [
				'array',
				'object',
				\stdClass::class,
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function convert(IContent $content, string $target = null): IContent {
			switch ($target) {
				case 'array':
					return new Content(json_decode($content->getContent(), true), 'array');
				case 'object':
				case \stdClass::class:
					return new Content(json_decode($content->getContent()), $target);
				case  null:
					return $content;
			}
			throw new UnsupportedConversionException(sprintf('Conversion from [%s] to [%s] is not supported by [%s]', $content->getType(), $target, static::class));
		}
	}
