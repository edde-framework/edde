<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Template\ITemplate;
	use Edde\Common\Converter\Content;

	class TemplateContent extends Content {
		public function __construct(ITemplate $template) {
			parent::__construct($template, ITemplate::class);
		}
	}
