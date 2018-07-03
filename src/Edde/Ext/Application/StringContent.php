<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Converter\Content;

	/**
	 * Basically text/plain response.
	 */
	class StringContent extends Content {
		public function __construct(string $content) {
			parent::__construct($content, 'text/plain');
		}
	}
