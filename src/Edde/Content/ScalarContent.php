<?php
	declare(strict_types=1);
	namespace Edde\Content;

	class ScalarContent extends Content {
		public function __construct($content) {
			parent::__construct($content, 'scalar');
		}
	}
