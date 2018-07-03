<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	class HttpLinkGenerator extends AbstractLinkGenerator {
		public function link($generate, ...$parameterList) {
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (strpos($generate, 'http') === false) {
				return null;
			}
			return $generate;
		}
	}
