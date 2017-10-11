<?php
	declare(strict_types=1);
	namespace Edde\Common\Content;

		class JsonContent extends Content {
			public function __construct(string $content) {
				parent::__construct($content, 'application/json');
			}
		}
