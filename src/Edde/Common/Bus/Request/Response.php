<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus\Request;

	use Edde\Api\Bus\Request\IResponse;
	use Edde\Common\Bus\Element;

	class Response extends Element implements IResponse {
		public function __construct(string $uuid, array $attributes = [], array $metas = []) {
			parent::__construct('response', $uuid, $attributes, $metas);
		}
	}
