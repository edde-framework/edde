<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Converter\Content;

	class JsonEncodedContent extends Content {
		public function __construct($content) {
			parent::__construct($content, 'json');
		}
	}
