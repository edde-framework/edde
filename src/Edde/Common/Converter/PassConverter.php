<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\IContent;

	class PassConverter extends AbstractConverter {
		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			return new Content($content, $mime);
		}
	}
