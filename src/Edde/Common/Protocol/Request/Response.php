<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Common\Protocol\Element;

	class Response extends Element {
		public function __construct(string $id = null) {
			parent::__construct('response', $id);
		}
	}
