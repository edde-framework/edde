<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Common\Converter\Content;

	class ArrayContent extends Content {
		public function __construct(array $content) {
			parent::__construct($content, 'array');
		}
	}
