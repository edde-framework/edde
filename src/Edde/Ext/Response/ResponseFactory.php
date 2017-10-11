<?php
	declare(strict_types=1);
	namespace Edde\Ext\Response;

		use Edde\Api\Element\IElement;

		/**
		 * Response factory is helper trait to send the response; whole workflow is now
		 * in one place in hands of a developer, so it's much simpler to control what happens
		 * in an application.
		 */
		trait ResponseFactory {
			public function json($content) {
			}

			public function text(string $content) {
			}

			public function element(IElement $element) {
			}
		}
