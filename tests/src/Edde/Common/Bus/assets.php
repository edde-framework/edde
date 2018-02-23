<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\Request\IResponse;
	use Edde\Common\Bus\Event\AbstractListener;
	use Edde\Common\Bus\Request\Response;
	use Edde\Common\Object\Object;

	class CommonListener extends AbstractListener {
		/** @inheritdoc */
		public function getListeners(): iterable {
			yield 'foo' => [$this, 'eventFoo'];
			yield 'bar' => [$this, 'eventBar'];
		}

		public function eventFoo(IElement $event, IElement $response) {
			$response->setAttribute('foo-was-here', true);
		}

		public function eventBar(IElement $event, IElement $response) {
			$response->setAttribute('bar-was-here', true);
		}
	}

	class SomeService extends Object {
		public function requestedMethod(): IResponse {
			$response = new Response('foo');
			$response->setAttribute('yaay', true);
			return $response;
		}
	}
