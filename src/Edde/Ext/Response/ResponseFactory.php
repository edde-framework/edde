<?php
	declare(strict_types=1);
	namespace Edde\Ext\Response;

		use Edde\Api\Content\IContent;
		use Edde\Api\Converter\Inject\ConverterManager;
		use Edde\Api\Element\IElement;
		use Edde\Api\Request\Inject\RequestService;

		/**
		 * Response factory is helper trait to send the response; whole workflow is now
		 * in one place in hands of a developer, so it's much simpler to control what happens
		 * in an application.
		 */
		trait ResponseFactory {
			use RequestService;
			use ConverterManager;

			public function json($content) {
			}

			public function text(string $content) {
			}

			public function element(IElement $element) {
			}

			public function send(IContent $content) {
//				$this->converterManager->convert($content, $this->requestService->getRequest()->getTargetList());
			}
		}
