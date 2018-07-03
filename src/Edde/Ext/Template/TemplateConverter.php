<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;

	class TemplateConverter extends AbstractConverter {
		public function __construct() {
			$this->register(ITemplate::class, [
				'text/html',
				'text/*',
				'*/*',
				'string',
			]);
		}

		/**
		 * @inheritdoc
		 *
		 * @param ITemplate $content
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, $content instanceof ITemplate);
			switch ($target) {
				case 'text/html':
				case 'text/*':
				case '*/*':
				case 'string':
					return new Content($content->string(), 'text/html');
			}
			return $this->exception($mime, $target);
		}
	}
