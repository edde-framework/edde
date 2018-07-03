<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\IContent;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;

	class PostConverter extends AbstractConverter {
		public function __construct() {
			$this->register('array', [
				'application/x-www-form-urlencoded',
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, is_array($content));
			switch ($target) {
				case 'application/x-www-form-urlencoded':
					return new Content(http_build_query($content), $target);
			}
			return $this->exception($mime, $target);
		}
	}
