<?php
	declare(strict_types=1);
	namespace Edde\Ext\Converter;

	use Edde\Api\Content\IContent;
	use Edde\Common\Content\Content;
	use Edde\Common\Converter\AbstractConverter;

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
