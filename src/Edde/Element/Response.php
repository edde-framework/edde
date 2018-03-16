<?php
	declare(strict_types=1);
	namespace Edde\Element;

	class Response extends Element implements \Edde\Element\IResponse {
		public function __construct(string $uuid, array $attributes = [], array $metas = []) {
			parent::__construct('response', $uuid, $attributes, $metas);
		}
	}
