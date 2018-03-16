<?php
	declare(strict_types=1);
	namespace Edde\Ext\Converter;

	use Edde\Content\Content;
	use Edde\Content\IContent;
	use Edde\Converter\AbstractConverter;

	class JsonEncodeConverter extends AbstractConverter {
		public function __construct() {
			parent::__construct([
				'array',
				'object',
				\stdClass::class,
			], [
				'application/json',
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function convert(IContent $content, string $target = null): IContent {
			return new Content(json_encode($content->getContent()), $target);
		}
	}
