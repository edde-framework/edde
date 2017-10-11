<?php
	declare(strict_types=1);
	namespace Edde\Ext\Response;

		use Edde\Api\Content\IContent;
		use Edde\Api\Converter\Inject\ConverterManager;
		use Edde\Api\Element\IElement;
		use Edde\Api\Request\Inject\RequestService;
		use Edde\Ext\Content\ContentFactory;

		/**
		 * Response factory is helper trait to send the response; whole workflow is now
		 * in one place in hands of a developer, so it's much simpler to control what happens
		 * in an application.
		 */
		trait ResponseFactory {
			use ContentFactory;
			use RequestService;
			use ConverterManager;

			public function sendScalar($content) {
				$this->send($this->contentScalar($content));
			}

			public function sendJson(string $content) {
				$this->send($this->contentJson($content));
			}

			public function sendText(string $content) {
				$this->send($this->contentText($content));
			}

			public function sendElement(IElement $element) {
				$this->send($this->contentElement($element));
			}

			public function send(IContent $content) {
//				$this->converterManager->convert($content, $this->requestService->getRequest()->getTargetList());
			}
		}
