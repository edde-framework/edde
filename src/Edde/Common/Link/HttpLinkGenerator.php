<?php
	declare(strict_types=1);

	namespace Edde\Common\Link;

	class HttpLinkGenerator extends AbstractLinkGenerator {
		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			list($generate) = $this->list($generate, $parameterList);
			if (strpos($generate, 'http') === false) {
				return null;
			}
			return $generate;
		}
	}
