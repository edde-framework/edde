<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Content\Content;
	use Edde\Content\IContent;

	class PassConverter extends AbstractConverter {
		/** @inheritdoc */
		public function __construct() {
			parent::__construct([], []);
		}

		/** @inheritdoc */
		public function convert(IContent $content, string $target = null): IContent {
			return new Content($content->getContent(), $target);
		}
	}
